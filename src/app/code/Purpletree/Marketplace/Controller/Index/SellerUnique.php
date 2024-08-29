<?php

/**
 * Purpletree_Marketplace SellerUnique
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
 
namespace Purpletree\Marketplace\Controller\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class SellerUnique extends \Magento\Framework\App\Action\Action
{
    /**
     * @param Context
     * @param \Magento\Framework\Controller\Result\JsonFactory
     * @param \Magento\Framework\Json\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     */

     public $resultJsonFactory;
     public $uniqueUrl;
     public $_resultPage;

    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $uniqueUrl
    ) {
        parent::__construct($context);
        $this->resultJsonFactory        =       $resultJsonFactory;
        $this->uniqueUrl                =       $uniqueUrl;
    }

    public function execute()
    {
        $response = [];
        $data = $this->getRequest()->getPostValue();
        $result = $this->resultJsonFactory->create();
        if ($this->getRequest()->isAjax()) {
            $data1 = [];
            $data1=sizeof($this->getUniqueUrl($data["store_url"]));
            return $result->setData($data1);
        }
    }

    /**
     * Get Unique URL
     *
     * @return String
     */
    public function getUniqueUrl($storeurl)
    {
        return $this->uniqueUrl->checkUniqueUrl($storeurl);
    }
}
