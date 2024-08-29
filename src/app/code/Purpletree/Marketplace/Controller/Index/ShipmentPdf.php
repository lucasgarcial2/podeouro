<?php
/**
 * Purpletree_Marketplace ShipmentPdf
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

class ShipmentPdf extends Action
{
    /**
     * Constructor
     *
     * @param Context $context
     * @param DompdfFactory $dompdfFactory
     *
     */
    public $shipmentPdf;
    public $shipmentModel;
    public $dataHelper;
    public $storeManager;
    public $_fileFactory;
    public $storeDetails;
    public $customer;
    public $dateTime;
    public $_resultPage;

    public function __construct(
        Context $context,
        CustomerSession $customer,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Sales\Model\Order\Shipment $shipmentModel,
        \Magento\Sales\Model\Order\Pdf\Shipment $shipmentPdf,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->shipmentPdf =     $shipmentPdf;
        $this->shipmentModel =     $shipmentModel;
        $this->dataHelper =     $dataHelper;
        $this->storeManager = $storeManager;
        $this->_fileFactory = $fileFactory;
        $this->storeDetails = $storeDetails;
        $this->customer = $customer;
        $this->dateTime = $dateTime;
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
        $shipmentId = $this->getRequest()->getParam('shipment_id');
        if ($shipmentId) {
            $shipment = $this->shipmentModel->load($shipmentId);
            if ($shipment) {
                $pdf = $this->shipmentPdf->getPdf(
                    [$shipment]
                );
                $date = $this->dateTime->date('Y-m-d_H-i-s');
                return $this->_fileFactory->create(
                    'packingslip' . $date . '.pdf',
                    $pdf->render(),
                    DirectoryList::VAR_DIR,
                    'application/pdf'
                );
            }
        } else {
            /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
    }
    public function getSellerId()
    {
        $customerId=$this->customer->getCustomer()->getId();
        return $this->storeDetails->isSeller($customerId);
    }
}
