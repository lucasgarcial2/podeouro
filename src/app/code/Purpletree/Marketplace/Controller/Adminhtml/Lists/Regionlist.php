<?php

/**
 * Purpletree_Marketplace Regionlist
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

namespace Purpletree\Marketplace\Controller\Adminhtml\Lists;

class Regionlist extends \Magento\Framework\App\Action\Action
{
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public $jsonHlper;
    public $_countryFactory;
    public $resultPageFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Json\Helper\Data $jsonHlper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->jsonHlper = $jsonHlper;
        $this->_countryFactory = $countryFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
         $result['success']='false';
         $countrycode = $this->getRequest()->getpost('country');
        if ($countrycode != '') {
            $state = "<option value=''>--Please Select--</option>";
            $havestates = 0;
            $statearray =$this->_countryFactory->create()->setId(
                $countrycode
            )->getLoadedRegionCollection()->toOptionArray();
            foreach ($statearray as $_state) {
                if ($_state['value']) {
                    $state .= "<option >" . $_state['label'] . "</option>";
                    $havestates =1;
                }
            }
            if ($havestates == 1) {
                $result['success']='true';
                $result['htmlconent']=$state;
            }
        }
      
         $this->getResponse()->representJson(
             $this->jsonHlper->jsonEncode($result)
         );
    }
}
