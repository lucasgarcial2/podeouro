<?php
/**
 * Purpletree_Marketplace CustomerEnquire
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

class CustomerEnquire extends \Magento\Framework\View\Element\Template
{
    protected $vendorContact;
    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options
     * @param \Purpletree\Marketplace\Model\ResourceModel\Reviews
     * @param \Magento\Framework\Registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Purpletree\Marketplace\Model\VendorContactFactory
     * @param array $data
     */

     public $coreRegistry;
     public $vendorContactCollectionFactory;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Purpletree\Marketplace\Model\VendorContactFactory $vendorContactCollectionFactory,
        array $data = []
    ) {
        $this->coreRegistry                 =       $coreRegistry;
        $this->vendorContactCollectionFactory     =       $vendorContactCollectionFactory;
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getEnquire()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'seller.enquire.pager'
            )->setCollection(
                $this->getEnquire()
            );
            $this->setChild('pager', $pager);
            $this->getEnquire();
        }
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
 
    /**
     * Seller ID
     *
     * @return Seller ID
     */
    public function getSellerId()
    {
        return $this->coreRegistry->registry('seller_Id');
    }
    
    /**
     * Get Enquire
     *
     * @return Enquire
     */
    public function getEnquire()
    {
    
        if (!$this->vendorContact) {
            $collection = $this->vendorContactCollectionFactory->create();
            $this->vendorContact = $collection->getCollection()
                            ->addFieldToFilter('seller_id', $this->getSellerId());
        }
        return $this->vendorContact;
    }
}
