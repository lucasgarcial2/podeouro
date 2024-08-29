<?php
/**
 * Purpletree_Marketplace InlineEdit
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

abstract class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * constructor
     *
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Purpletree\Marketplace\Model\PaymentsFactory $paymentsFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public $_jsonFactory;
    public $_paymentsFactory;

    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Purpletree\Marketplace\Model\PaymentsFactory $paymentsFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_jsonFactory = $jsonFactory;
        $this->_paymentsFactory = $paymentsFactory;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Marketplace::payments');
    }
    
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultJson = $this->_jsonFactory->create();
        $error = false;
        $messages = [];
        $paymentsItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && $paymentsItems && count($paymentsItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }
        foreach (array_keys($paymentsItems) as $paymentsId) {
            $payments = $this->_paymentsFactory->create()->load($paymentsId);
            try {
                $paymentsData = $paymentsItems[$paymentsId];//todo: handle dates
                $payments->addData($paymentsData);
                $payments->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithPaymentsId($payments, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithPaymentsId($payments, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPaymentsId(
                    $payments,
                    __('Something went wrong while saving the Payments.')
                );
                $error = true;
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Payments id to error message
     *
     * @param \Purpletree\Marketplace\Model\Payments $payments
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithPaymentsId(\Purpletree\Marketplace\Model\Payments $payments, $errorText)
    {
        return '[Payments ID: ' . $payments->getId() . '] ' . $errorText;
    }
}
