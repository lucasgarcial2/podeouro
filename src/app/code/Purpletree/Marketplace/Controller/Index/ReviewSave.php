<?php
/**
 * Purpletree_Marketplace ReviewSave
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
 
namespace Purpletree\Marketplace\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class ReviewSave extends Action
{

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Purpletree\Marketplace\Model\SellerFactory
     * @param \Magento\Framework\Registry
     * @param \Purpletree\Marketplace\Model\Reviews
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Magento\Framework\App\Action\Context
     *
     */
    public $customer;
    public $dataHelper;
    public $sellerReview;
    public $storeDetails;
    public $resultForwardFactory;
    public $_resultPage;


    public function __construct(
        \Magento\Customer\Model\Session $customer,
        \Purpletree\Marketplace\Model\Reviews $sellerReview,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        Context $context
    ) {
        $this->customer              =      $customer;
        $this->dataHelper            =      $dataHelper;
        $this->sellerReview          =      $sellerReview;
        $this->storeDetails          =      $storeDetails;
        $this->resultForwardFactory  =      $resultForwardFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $customerId=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        $reviewEnable=$this->dataHelper->getGeneralConfig('seller_review/seller_review_enabled');
        if (!$moduleEnable && !$reviewEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $this->sellerReview->setReviewTitle($data['review_title']);
                $this->sellerReview->setReviewDescription($data['review_description']);
                $this->sellerReview->setRating($data['rating']);
                $this->sellerReview->setSellerId($data['seller_id']);
                $this->sellerReview->setCustomerId($data['customer_id']);
                $aprroval = $this->dataHelper->getGeneralConfig('seller_review/seller_review_enabled');
                if ($aprroval == 1) {
                    $this->sellerReview->setStatus(0);
                } else {
                    $this->sellerReview->setStatus(1);
                }
                $this->sellerReview->save();
                $this->messageManager->addSuccess(__('Thankyou for your valuable review'));
                return $this->_redirect('marketplace/index/sellerreview/sellerid/'.$data['seller_id']);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the review'));
            }
        }
    }
}
