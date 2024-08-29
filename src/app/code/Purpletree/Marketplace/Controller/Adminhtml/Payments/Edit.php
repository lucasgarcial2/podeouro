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
 
namespace Purpletree\Marketplace\Controller\Adminhtml\Payments;

class Edit extends \Purpletree\Marketplace\Controller\Adminhtml\Payments
{
    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Purpletree\Marketplace\Model\PaymentsFactory $paymentsFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public $_resultPageFactory;
    public $_context;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Purpletree\Marketplace\Model\PaymentsFactory $paymentsFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_context = $context;
        parent::__construct($paymentsFactory, $registry, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Marketplace::payments');
    }

    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('entity_id');
            $payments = $this->_initPayments();
            $resultPage = $this->_resultPageFactory->create();
            $resultPage->setActiveMenu('Purpletree_Marketplace::payments');
            $resultPage->getConfig()->getTitle()->set(__('Payments'));
            if ($id) {
                $payments->load($id);
                if (!$payments->getId()) {
                    $this->messageManager->addError(__('This Payments no longer exists.'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath(
                        'purpletree_marketplace/payments/edit',
                        [
                        'entity_id' => $payments->getId(),
                        '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
            }
            $title = $payments->getId() ? $payments->getTitle() : __('New Payments');
            $resultPage->getConfig()->getTitle()->prepend($title);
            $data = $this->_context->getSession()->getData('purpletree_marketplace_payments_data', true);
            if (!empty($data)) {
                $payments->setData($data);
            }
            return $resultPage;
        } catch (\Exception $e) {
            // display error message
            $this->messageManager->addError($e->getMessage());
        }
         $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('purpletree_marketplace/payments');
                return $resultRedirect;
    }
}
