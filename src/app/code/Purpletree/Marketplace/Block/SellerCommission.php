<?php
/**
 * Purpletree_Marketplace SellerCommission
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

class SellerCommission extends \Magento\Framework\View\Element\Template
{
    /**
     * Commission
     */
    protected $commission;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options
     * @param \Magento\Framework\Registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface
     * @param \Magento\Directory\Model\Config\Source\Country
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Pricing\Helper\Data
     * @param \Purpletree\Marketplace\Model\CommissionFactory
     * @param \Magento\Sales\Model\Order
     * @param array $data
     */
    public $coreRegistry;
    public $commissionCollectionFactory;
    public $priceHelper;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Purpletree\Marketplace\Model\CommissionFactory $commissionCollectionFactory,
        array $data = []
    ) {
        $this->coreRegistry                 =       $coreRegistry;
        $this->commissionCollectionFactory  =       $commissionCollectionFactory;
        $this->priceHelper                  =       $priceHelper;
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCommission()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'seller.commission.pager'
            )->setCollection(
                $this->getCommission()
            );
            $this->setChild('pager', $pager);
            $this->getCommission();
        }
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    /**
     * Commission
     *
     * @return Commission
     */
    public function getCommission()
    {
        if (!$this->commission) {
                $collection = $this->commissionCollectionFactory->create();
            if ($this->getRequest()->isAjax()) {
                $data = $this->getRequest()->getPostValue();
                $fromDate=(isset($data['from']) && $data['from'] !='')?date('Y-m-d', strtotime($data['from'])):'';
                $toDate=(isset($data['report_to']) && $data['report_to'] !='')?date('Y-m-d', strtotime($data['report_to'])):'';
                $this->commission = $this->getCommissionAjax($fromDate, $toDate, $collection);
            } else {
                $this->commission = $collection->getCollection()->addFieldToSelect(
                    '*'
                )->addFieldToFilter('seller_id', $this->getSellerId());
            }
        }
        return $this->commission;
    }
    
    public function getCommissionAjax($fromDate, $toDate, $collection)
    {

        if ($fromDate =='' && $toDate =='') {
            $collectionss = $collection->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter('seller_id', $this->getSellerId());
        } elseif ($fromDate == '') {
            $collectionss = $collection->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'created_at',
                [
                    'lt'=>$toDate
                ]
            )->addFieldToFilter('seller_id', $this->getSellerId());
        } elseif ($toDate == '') {
            $collectionss = $collection->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'created_at',
                [
                    'gt'=>$fromDate
                    ]
            )->addFieldToFilter('seller_id', $this->getSellerId());
        } else {
            $collectionss = $collection->getCollection()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'created_at',
                [
                    'from'=>$fromDate,
                    'to'=>$toDate
                 ]
            )->addFieldToFilter('seller_id', $this->getSellerId());
        }
        return $collectionss;
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
     * Seller ID
     *
     * @return Seller ID
     */
    public function getSellerId()
    {
        return $this->coreRegistry->registry('seller_id');
    }
}
