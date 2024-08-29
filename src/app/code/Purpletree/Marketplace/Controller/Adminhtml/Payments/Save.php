<?php
/**
 * Purpletree_Marketplace Save
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

class Save extends \Purpletree\Marketplace\Controller\Adminhtml\Payments
{
    /**
     * constructor
     *
     * @param \Purpletree\Marketplace\Model\PaymentsFactory $paymentsFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public $_context;

    public function __construct(
        \Purpletree\Marketplace\Model\PaymentsFactory $paymentsFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_context = $context;
        parent::__construct($paymentsFactory, $registry, $context);
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Marketplace::payments');
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('payments');
        $resultRedirect = $this->resultRedirectFactory->create();
        
        if ($data) {
           try { 
            $payments = $this->_initPayments();
            $payments->setData($data);
            $this->_eventManager->dispatch(
                'purpletree_marketplace_payments_prepare_save',
                [
                    'payments' => $payments,
                    'request' => $this->getRequest()
                ]
            );
         
                try {
                    $payments->save();
                    $this->messageManager->addSuccess(__('The Payment has been saved.'));
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the Payment.'));
                }
               
                $this->_context->getSession()->setPurpletreeMarketplacePaymentsData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'purpletree_marketplace/payments/edit',
                        [
                            'entity_id' => $payments->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('purpletree_marketplace/payments/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Payment.'));
            }
            $this->_getSession()->setPurpletreeMarketplacePaymentsData($data);
            $resultRedirect->setPath(
                'purpletree_marketplace/payments/edit',
                [
                    'entity_id' => $payments->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
                
        $resultRedirect->setPath('purpletree_marketplace/payments/');
        return $resultRedirect;
    }
}
