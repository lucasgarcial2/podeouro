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
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
namespace Purpletree\Marketplace\Model\Config\Backend;

class Processdata extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        $label = $this->getData('field_config/label');
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $processdataa   = $objectManager->get('\Purpletree\Marketplace\Helper\Processdata');
        $datHelper      = $objectManager->get('\Purpletree\Marketplace\Helper\Data');
        $configwriter   = $objectManager->get('\Magento\Framework\App\Config\Storage\WriterInterface');
        $messageManager = $objectManager->get('\Magento\Framework\Message\ManagerInterface');
        if ($this->getValue() != '') {
             $datapro = $processdataa->getProcessingdata($this->getValue());
        } else {
            $dfd = base64_decode('TGljZW5zZSBjYW5ub3QgYmUgbGVmdCBibGFuay4=');
            throw new \Magento\Framework\Exception\ValidatorException(__($dfd));
        }
        if ($datapro) {
            $this->setValue($this->getValue());
            parent::beforeSave();
        } else {
            if ($datHelper->isEnabled() == 1) {
                $dcdcvdswq = base64_decode('cHVycGxldHJlZV9tYXJrZXRwbGFjZS9nZW5lcmFsL2VuYWJsZWQ=');
                $configwriter->save($dcdcvdswq, '0');
                $dswe = base64_decode('SW52YWxpZCBMaWNlbnNlLlBsZWFzZSBDb250YWN0IFN1cHBvcnQu');
                $messageManager->addError(__($dswe));
            } else {
                $fdcvb = base64_decode('SW52YWxpZCBMaWNlbnNlLlBsZWFzZSBDb250YWN0IFN1cHBvcnQu');
                 throw new \Magento\Framework\Exception\ValidatorException(__($fdcvb));
            }
        }
    }
}
