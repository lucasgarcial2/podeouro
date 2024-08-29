<?php
/**
 * Purpletree_Marketplace Edit
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
 
namespace Purpletree\Marketplace\Controller\Adminhtml\Categorycommission;

class Edit extends \Purpletree\Marketplace\Controller\Adminhtml\Categorycommission
{
    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Purpletree\Marketplace\Model\CategorycommissionFactory $categorycommissionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public $_resultPageFactory;
    public $_resultJsonFactory;
    public $_context;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Purpletree\Marketplace\Model\CategorycommissionFactory $categorycommissionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_context = $context;
        parent::__construct($categorycommissionFactory, $registry, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Marketplace::commission');
    }

    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('entity_id');
            $categorycommission = $this->_initCategorycommission();
            $resultPage = $this->_resultPageFactory->create();
            $resultPage->setActiveMenu('Purpletree_Marketplace::categorycommission');
            $resultPage->getConfig()->getTitle()->set(__('Category Commission'));
            if ($id) {
                $categorycommission->load($id);
                if (!$categorycommission->getId()) {
                    $this->messageManager->addError(__('This Category Commission no longer exists.'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath(
                        'purpletree_marketplace/categorycommission/edit',
                        [
                        'entity_id' => $categorycommission->getId(),
                        '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
            }
            $title = $categorycommission->getId() ? $categorycommission->getTitle() : __('New Category Commission');
            $resultPage->getConfig()->getTitle()->prepend($title);
            $data = $this->_context->getSession()->getData('purpletree_marketplace_categorycommission_data', true);
            if (!empty($data)) {
                $categorycommission->setData($data);
            }
            return $resultPage;
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
        }
         $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('purpletree_marketplace/categorycommission');
                return $resultRedirect;
    }
}
