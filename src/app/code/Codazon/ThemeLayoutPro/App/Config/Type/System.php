<?php
/**
 * Copyright © 2017 Codazon, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Codazon\ThemeLayoutPro\App\Config\Type;

use Magento\Framework\App\Config\ConfigTypeInterface;
use Magento\Framework\App\Config\ConfigSourceInterface;
use Magento\Framework\App\Config\Spi\PostProcessorInterface;
use Magento\Framework\App\Config\Spi\PreProcessorInterface;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\App\ObjectManager;
use Codazon\ThemeLayoutPro\App\Config\Type\System\Reader;
use Magento\Store\Model\Config\Processor\Fallback;

/**
 * System configuration type.
 */
class System implements ConfigTypeInterface
{
    /**
     * Cache tag.
     */
    const CACHE_TAG = 'themeconfig_scopes';

    /**
     * Config type.
     */
    const CONFIG_TYPE = 'theme_system';

    /**
     * Config source.
     *
     * @var ConfigSourceInterface
     */
    private $source;

    /**
     * Object data.
     *
     * @var array
     */
    private $data = [];

    /**
     * Postprocessor.
     *
     * @var PostProcessorInterface
     */
    private $postProcessor;

    /**
     * Preprocessor.
     *
     * @var PreProcessorInterface
     */
    private $preProcessor;

    /**
     * Cache.
     *
     * @var FrontendInterface
     */
    private $cache;

    /**
     * Caching nested level.
     *
     * @var int
     */
    private $cachingNestedLevel;

    /**
     * Fallback.
     *
     * @var Fallback
     */
    private $fallback;

    /**
     * The type of config.
     *
     * @var string
     */
    private $configType;

    /**
     * Reader.
     *
     * @var Reader
     */
    private $reader;

    /**
     * List of scopes that were retrieved from configuration storage.
     *
     * Is used to make sure that we don't try to load non-existing configuration scopes.
     *
     * @var array
     */
    private $availableDataScopes = null;
    protected $coreRegistry;

    /**
     * @param ConfigSourceInterface $source
     * @param PostProcessorInterface $postProcessor
     * @param Fallback $fallback
     * @param FrontendInterface $cache
     * @param PreProcessorInterface $preProcessor
     * @param int $cachingNestedLevel
     * @param string $configType
     * @param Reader $reader
     */
    public function __construct(
        ConfigSourceInterface $source,
        PostProcessorInterface $postProcessor,
        Fallback $fallback,
        FrontendInterface $cache,
        PreProcessorInterface $preProcessor,
        \Magento\Framework\Registry $coreRegistry,
        $configType = self::CONFIG_TYPE,
        Reader $reader = null,
        $cachingNestedLevel = 1
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->source = $source;
        $this->postProcessor = $postProcessor;
        $this->preProcessor = $preProcessor;
        $this->cache = $cache;
        $this->cachingNestedLevel = $cachingNestedLevel;
        $this->fallback = $fallback;
        $this->configType = $configType;
        $this->reader = $reader ?: ObjectManager::getInstance()->get(Reader::class);
    }

    /**
     * System configuration is separated by scopes (default, websites, stores). Configuration of a scope is inherited
     * from its parent scope (store inherits website).
     *
     * Because there can be many scopes on single instance of application, the configuration data can be pretty large,
     * so it does not make sense to load all of it on every application request. That is why we cache configuration
     * data by scope and only load configuration scope when a value from that scope is requested.
     *
     * Possible path values:
     * '' - will return whole system configuration (default scope + all other scopes)
     * 'default' - will return all default scope configuration values
     * '{scopeType}' - will return data from all scopes of a specified {scopeType} (websites, stores)
     * '{scopeType}/{scopeCode}' - will return data for all values of the scope specified by {scopeCode} and scope type
     * '{scopeType}/{scopeCode}/some/config/variable' - will return value of the config variable in the specified scope
     *
     * @inheritdoc
     */
    public function get($path = '')
    {

        if ($path === '') {
            $this->data = array_replace_recursive($this->loadAllData(), $this->data);

            return $this->data;
        }
        $pathParts = explode('/', $path);
        
        if ($currentTheme = $this->coreRegistry->registry('current_theme')) {
            $themeId = $currentTheme->getId();
        } else {
            $themeId = '';
        }
        if (count($pathParts) === 1 && $pathParts[0] !== 'default') {
            if (!isset($this->data[$pathParts[0]])) {
                $data = $this->reader->read();
                $this->data = array_replace_recursive($data, $this->data);
            }

            return $this->data[$pathParts[0]];
        }
        $scopeType = array_shift($pathParts);
        if ($scopeType === 'default') {
            if (!isset($this->data[$scopeType])) {
                $this->data = array_replace_recursive($this->loadDefaultScopeData($scopeType, $themeId), $this->data);
            }

            return $this->getDataByPathParts($this->data[$scopeType], $pathParts);
        }
        $scopeId = array_shift($pathParts);
        if (!isset($this->data[$scopeType][$scopeId])) {
            $this->data = array_replace_recursive($this->loadScopeData($scopeType, $scopeId, $themeId), $this->data);
        }

        return isset($this->data[$scopeType][$scopeId])
            ? $this->getDataByPathParts($this->data[$scopeType][$scopeId], $pathParts)
            : null;
    }

    /**
     * Load configuration data for all scopes.
     *
     * @return array
     */
    private function loadAllData()
    {
        $cachedData = $this->cache->load($this->configType);
        if ($cachedData === false) {
            $data = $this->reader->read();
        } else {
            $data = unserialize($cachedData);
        }

        return $data;
    }

    /**
     * Load configuration data for default scope.
     *
     * @param string $scopeType
     * @return array
     */
    private function loadDefaultScopeData($scopeType, $themeId)
    {
        $cachedData = $this->cache->load($this->configType . '_' . $scopeType . '_' . $themeId);
        
        if ($cachedData === false) {
            $data = $this->reader->read();
            $this->cacheData($data, $themeId);
        } else {
            $data = [$scopeType => unserialize($cachedData)];
        }

        return $data;
    }

    /**
     * Load configuration data for a specified scope.
     *
     * @param string $scopeType
     * @param string $scopeId
     * @return array
     */
    private function loadScopeData($scopeType, $scopeId, $themeId)
    {
        $cachedData = $this->cache->load($this->configType . '_' . $scopeType . '_' . $scopeId . '_' . $themeId);
        
        if ($cachedData === false) {
            if ($this->availableDataScopes === null) {
                $cachedScopeData = $this->cache->load($this->configType . '_scopes'. '_' . $themeId);
                if ($cachedScopeData !== false) {
                    $this->availableDataScopes = unserialize($cachedScopeData);
                }
            }
            if (is_array($this->availableDataScopes) && !isset($this->availableDataScopes[$scopeType][$scopeId])) {
                return [$scopeType => [$scopeId => []]];
            }
            $data = $this->reader->read();
            $this->cacheData($data, $themeId);
        } else {
            $data = [$scopeType => [$scopeId => unserialize($cachedData)]];
        }

        return $data;
    }

    /**
     * Cache configuration data.
     * Caches data per scope to avoid reading data for all scopes on every request.
     *
     * @param array $data
     * @return void
     */
    private function cacheData(array $data, $themeId)
    {
        $this->cache->save(
            serialize($data),
            $this->configType . '_' . $themeId,
            [self::CACHE_TAG]
        );
        $this->cache->save(
            serialize($data['default']),
            $this->configType . '_default' . '_' . $themeId,
            [self::CACHE_TAG]
        );
        $scopes = [];
        foreach (['websites', 'stores'] as $curScopeType) {
            foreach ($data[$curScopeType] as $curScopeId => $curScopeData) {
                $scopes[$curScopeType][$curScopeId] = 1;
                $this->cache->save(
                    serialize($curScopeData),
                    $this->configType . '_' . $curScopeType . '_' . $curScopeId . '_' . $themeId,
                    [self::CACHE_TAG]
                );
            }
        }
        $this->cache->save(
            serialize($scopes),
            $this->configType . "_scopes" . '_' . $themeId,
            [self::CACHE_TAG]
        );
    }

    /**
     * Walk nested hash map by keys from $pathParts.
     *
     * @param array $data to walk in
     * @param array $pathParts keys path
     * @return mixed
     */
    private function getDataByPathParts($data, $pathParts)
    {
        foreach ($pathParts as $key) {
            if ((array)$data === $data && isset($data[$key])) {
                $data = $data[$key];
            } elseif ($data instanceof \Magento\Framework\DataObject) {
                $data = $data->getDataByKey($key);
            } else {
                return null;
            }
        }

        return $data;
    }

    /**
     * Clean cache and global variables cache.
     *
     * @return void
     */
    public function clean()
    {
        $this->data = [];
        $this->cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, [self::CACHE_TAG]);
        $this->availableDataScopes = null;
    }
}
