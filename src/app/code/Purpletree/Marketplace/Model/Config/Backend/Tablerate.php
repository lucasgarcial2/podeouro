<?php
/**
* Purpletree_Marketplace Tablerate
* NOTICE OF LICENSE
*
* This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
* It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
*
* @category    Purpletree
* @package     Purpletree_Marketplace
* @author      Purpletree Software
* @copyright   Copyright (c) 2020
* @license     https://www.purpletreesoftware.com/license.html
*/
namespace Purpletree\Marketplace\Model\Config\Backend;

use Magento\Framework\Model\AbstractModel;

/**
 * Backend model for shipping table rates CSV importing
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Tablerate extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Purpletree\Marketplace\Model\ResourceModel\Carrier\TablerateFactory
     */
    protected $_tablerateFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Purpletree\Marketplace\Model\ResourceModel\Carrier\TablerateFactory $tablerateFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Purpletree\Marketplace\Model\ResourceModel\Carrier\TablerateFactory $tablerateFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_tablerateFactory = $tablerateFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this
     */
    public function afterSave()
    {
        /** @var \Purpletree\Marketplace\Model\ResourceModel\Carrier\Tablerate $tableRate */
        $tableRate = $this->_tablerateFactory->create();
        $tableRate->uploadAndImport($this);
        return parent::afterSave();
    }
}
