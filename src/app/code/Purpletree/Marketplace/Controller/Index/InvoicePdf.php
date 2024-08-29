<?php
/**
 * Purpletree_Marketplace InvoicePdf
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
namespace Purpletree\Marketplace\Controller\Index;

use \Magento\Framework\App\Action\Action;
use \Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Customer\Model\Session as CustomerSession;

class InvoicePdf extends Action
{

    /**
     * Constructor
     *
     * @param Context $context
     * @param DompdfFactory $dompdfFactory
     *
     */
    public $InvoiceRepositoryInterface;
    public $InvoicePdf;
    public $dateTime;
    public $dataHelper;
    public $storeManager;
    public $storeDetails;
    public $customer;
    public $_fileFactory;
    public $_resultPage;

    public function __construct(
        Context $context,
        CustomerSession $customer,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Sales\Model\Order\Pdf\Invoice $InvoicePdf,
        \Magento\Sales\Api\InvoiceRepositoryInterface $InvoiceRepositoryInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->InvoiceRepositoryInterface =     $InvoiceRepositoryInterface;
        $this->InvoicePdf =     $InvoicePdf;
        $this->dateTime =     $dateTime;
        $this->dataHelper =     $dataHelper;
        $this->storeManager = $storeManager;
        $this->storeDetails = $storeDetails;
        $this->customer = $customer;
         $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $sellerId=$this->getSellerId();
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        if ($sellerId=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
         $invoiceId  = $this->getRequest()->getParam('invoice_id');
        if ($invoiceId) {
            $invoice = $this->InvoiceRepositoryInterface->get($invoiceId);
            if ($invoice) {
                $pdf = $this->invoicePdf->getPdf([$invoice]);
                $date = $this->dateTime->date('Y-m-d_H-i-s');
                return $this->_fileFactory->create(
                    'invoice' . $date . '.pdf',
                    $pdf->render(),
                    DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }
        } else {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        return $response;
    }
    public function getSellerId()
    {
        $customerId=$this->customer->getCustomer()->getId();
        return $this->storeDetails->isSeller($customerId);
    }
}
