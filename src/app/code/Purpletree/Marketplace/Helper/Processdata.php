<?php
/**
 * Purpletree_Marketplace Processdata
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
namespace Purpletree\Marketplace\Helper;

use Magento\Store\Model\ScopeInterface;

class Processdata extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var string
     */
    const MODULE_NAME                      =   'purpletree_marketplace';
    
    public $_encryptor;
    public $writeInterface;
    public $_date;
    public $_cacheTypeList;
    public $_cacheFrontendPool;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\App\Config\Storage\WriterInterface $writeInterface,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    ) {
         $this->_encryptor = $encryptor;
         $this->writeInterface = $writeInterface;
          $this->_date = $date;
         $this->_cacheTypeList = $cacheTypeList;
         $this->_cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::MODULE_NAME . $code, $storeId);
    }
    public function getProcessingdata($data = null, $storeId = null)
    {
        $datafield = $this->getGeneralConfig('/general/process_data', $storeId);
        if ($data != null) {
            return $this->checkcurldata($data);
        } elseif ($datafield != null) {
            return $this->checkcurldata($datafield);
        }
        return false;
    }
    protected function checkcurldata($valuee)
    {
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            $domain = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
        } else {
            $domain = 'http://'.$_SERVER['HTTP_HOST'];
        }
         $text = $this->_encryptor->decrypt($this->getGeneralConfig('/seller_review/encypt_text'));
            $fdcvcv = base64_decode('KGxvY2FsaG9zdHxkZW1vfHRlc3Qp');
        if (preg_match($fdcvcv, $domain) && $domain == $text) {
            return true;
        } elseif (preg_match($fdcvcv, $domain) && $domain != $text) {
            return $this->checkdatanow($domain, $valuee);
        } else {
            $data_live = $this->getGeneralConfig('/seller_review/live_validate_text');
            if ($domain == $text && $data_live == 1) {
                return true;
            } else {
                return $this->checkdatanow($domain, $valuee);
            }
        }
    }
    protected function clear_cache()
    {
        $types = ['config'];
        foreach ($types as $type) {
                $this->_cacheTypeList->cleanType($type);
        }
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
    protected function checkdatanow($domain, $valuee)
    {
           $fdcvcv = base64_decode('KGxvY2FsaG9zdHxkZW1vfHRlc3Qp');
        $domain1        = $this->_encryptor->encrypt($domain);
        $module         = base64_decode('cHVycGxldHJlZV9tYXJrZXRwbGFjZQ==');
        $configwriter   = $this->writeInterface;
        $ip_address     = $this->get_client_ip();
        $url            = base64_decode('aHR0cHM6Ly93d3cucHJvY2Vzcy5wdXJwbGV0cmVlc29mdHdhcmUuY29tL29jY2hlY2tkYXRhLnBocA==');
        $handle         = curl_init($url);
                curl_setopt($handle, CURLOPT_VERBOSE, true);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt(
                    $handle,
                    CURLOPT_POSTFIELDS,
                    "process_data=$valuee&domain_name=$domain&ip_address=$ip_address&module_name=$module"
                );
                $result  = curl_exec($handle);
                $result1 = json_decode($result);
        if ($result1->status != 'success') {
            $cvdswq = base64_decode('cHVycGxldHJlZV9tYXJrZXRwbGFjZS9nZW5lcmFsL2VuYWJsZWQ=');
            $configwriter->save($cvdswq, '0');
            $bb = base64_decode('SW52YWxpZCwgTGFzdCB0cmllZCBhdCA=');
            $configwriter->save('purpletree_marketplace/general/process_data_field', $bb.$this->_date->date());
            $this->clear_cache();
            return false;
        } else {
            if (preg_match($fdcvcv, $domain)) {
            } else {
                $configwriter->save('purpletree_marketplace/seller_review/live_validate_text', '1');
            }
            $configwriter->save('purpletree_marketplace/seller_review/encypt_text', $domain1);
            $bb = base64_decode("VmFsaWRhdGVkIGF0");
            $configwriter->save('purpletree_marketplace/general/process_data_field', $bb.$this->_date->date());
            $this->clear_cache();
            return true;
        }
    }
    protected function get_client_ip()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } else {
            $ipaddress = 'UNKNOWN';
        }
            return $ipaddress;
    }
}
