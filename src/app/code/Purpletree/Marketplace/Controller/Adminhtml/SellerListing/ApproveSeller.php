<?php
/**
 * Purpletree_Marketplace Index
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

namespace Purpletree\Marketplace\Controller\Adminhtml\SellerListing;

class ApproveSeller extends \Magento\Backend\App\Action
{
    /**
     * constructor
     *
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    
    public $_sellerFactory;
    public $sellercustom;

    public function __construct(
        \Purpletree\Marketplace\Model\SellerFactory $sellerFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $sellercustom,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_sellerFactory = $sellerFactory;
            $this->sellercustom = $sellercustom;
        parent::__construct($context);
    }
    
    /**
     * execute the action
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            try {
                $sellerdata = $this->_initSeller($data);
                $sellerdata->setStatusId(1);
                $sellerdata->save();
                $this->messageManager->addSuccess(__('Seller has been Approved.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while approving Seller.'));
            }
        }
        $resultRedirect->setPath('purpletree_marketplace/sellerlisting/index/');
        return $resultRedirect;
    }
         /**
          * Init Seller
          *
          * @return \Purpletree\Marketplace\Model\Post
          */
    protected function _initSeller($customerId)
    {
        /** @var \Purpletree\Marketplace\Model\Post $post */
        $seller    = $this->_sellerFactory->create();
        $id = $this->sellercustom->getsellerEntityId($customerId);
           $seller->load($id);
        return $seller;
    }
}
