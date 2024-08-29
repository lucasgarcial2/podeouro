<?php
/**
 * Purpletree_Marketplace SellerPayment
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2020
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Purpletree\Marketplace\Block;

class SellerPayment extends \Magento\Framework\View\Element\Template
{
    /**
     * Payment
     */
    protected $payment;
    
    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options
     * @param \Magento\Framework\Registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Pricing\Helper\Data
     * @param \Purpletree\Marketplace\Model\PaymentsFactory
     * @param \Purpletree\Marketplace\Model\ResourceModel\Payments
     * @param array $data
     */

     public $sellrorder;
     public $coreRegistry;
     public $paymentCollectionFactory;
     public $saleDetails;
     public $paymentsDetails;
     public $priceHelper;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Purpletree\Marketplace\Model\PaymentsFactory $paymentCollectionFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Commission $saleDetails,
		\Purpletree\Marketplace\Model\ResourceModel\Sellerorder $sellrorder,
        \Purpletree\Marketplace\Model\ResourceModel\Payments $paymentsDetails,
        array $data = []
    ) {
        $this->sellrorder                 =       $sellrorder;
        $this->coreRegistry                 =       $coreRegistry;
        $this->paymentCollectionFactory     =       $paymentCollectionFactory;
        $this->saleDetails                  =       $saleDetails;
        $this->paymentsDetails              =       $paymentsDetails;
        $this->priceHelper                  =       $priceHelper;
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getPayments()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'seller.payment.pager'
            )->setCollection(
                $this->getPayments()
            );
            $this->setChild('pager', $pager);
            $this->getPayments();
        }
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    /**
     * Payments
     *
     * @return Payments
     */
    public function getPayments()
    {
        if (!$this->payment) {
            $collection = $this->paymentCollectionFactory->create();
            if ($this->getRequest()->isAjax()) {
                $data = $this->getRequest()->getPostValue();
                $fromDate     =   (isset($data['from']) && $data['from']!='')?date('Y-m-d', strtotime($data['from'])):'';
                $toDate       = (isset($data['report_to']) && $data['report_to']!='')?date('Y-m-d', strtotime($data['report_to'])):'';
                $this->payment = $this->getPaymentsAjax($fromDate, $toDate, $collection);
            } else {
                $this->payment = $collection->getCollection()->addFieldToFilter('seller_id', $this->getSellerId());
            }
        }
        return $this->payment;
    }
    public function getPaymentsAjax($fromDate, $toDate, $collection)
    {
        if ($fromDate =='' && $toDate =='') {
            $payments = $collection->getCollection()->addFieldToFilter('seller_id', $this->getSellerId());
        } elseif ($fromDate == '') {
            $payments = $collection->getCollection()->addFieldToFilter(
                'created_at',
                [
                'lt'=>$toDate
                ]
            )->addFieldToFilter('seller_id', $this->getSellerId());
        } elseif ($toDate == '') {
            $payments = $collection->getCollection()->addFieldToFilter(
                'created_at',
                [
                'gt'=>$fromDate
                ]
            )->addFieldToFilter('seller_id', $this->getSellerId());
        } else {
            $payments = $collection->getCollection()->addFieldToFilter(
                'created_at',
                [
                    'from'=>$fromDate,
                    'to'=>$toDate
                 ]
            )->addFieldToFilter('seller_id', $this->getSellerId());
        }
        return $payments;
    }
    /**
     * Formatted Price
     *
     * @return Formatted Price
     */
    public function getFormattedPrice($price)
    {
        $price=$this->priceHelper->currency($price, true, false);
        return $price;
    }
    
    /**
     *
     *
     * @return Seller ID
     */
    public function getSellerId()
    {
        return $this->coreRegistry->registry('seller_id');
    }
    
    /**
     *
     *
     * @return Sale Details
     */
    public function getSaleDetails()
    {
        return $this->saleDetails->getSaleDetails($this->getSellerId());
    }
    public function getTotalShipping()
    {
		$totlashipping = 0;
        $allshippings = $this->sellrorder->getTotalShipping($this->getSellerId());
		if(!empty($allshippings)) {
			foreach($allshippings as $shi) {
				if($shi['shipping']) {
					$totlashipping += $shi['shipping'];
				}
			}
		}
		//$totlashipping = 0;
		return $totlashipping;
    }
    
    /**
     *
     *
     * @return Payments Details
     */
    public function getPaytmentDetails()
    {
        return $this->paymentsDetails->getPaytmentDetails($this->getSellerId());
    }
}
