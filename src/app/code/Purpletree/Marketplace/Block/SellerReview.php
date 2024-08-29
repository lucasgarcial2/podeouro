<?php
/**
 * Purpletree_Marketplace SellerReview
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

class SellerReview extends \Magento\Framework\View\Element\Template
{
    /**
     * Reviews
     *
     */
    protected $reviews;
 
    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options
     * @param \Purpletree\Marketplace\Model\ResourceModel\Reviews
     * @param \Magento\Framework\Registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Purpletree\Marketplace\Model\ReviewsFactory
     * @param array $data
     */
    public $coreRegistry;
    public $reviewsDetails;
    public $customerRepositoryInterface;
    public $reviewsCollectionFactory;

    public function __construct(
        \Purpletree\Marketplace\Model\ResourceModel\Reviews $reviewsDetails,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Framework\View\Element\Template\Context $context,
        \Purpletree\Marketplace\Model\ReviewsFactory $reviewsCollectionFactory,
        array $data = []
    ) {
        $this->coreRegistry                 =       $coreRegistry;
        $this->reviewsDetails               =       $reviewsDetails;
        $this->customerRepositoryInterface  =       $customerRepositoryInterface;
        $this->reviewsCollectionFactory     =       $reviewsCollectionFactory;
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getReviews()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'seller.review.pager'
            )->setCollection(
                $this->getReviews()
            );
            $this->setChild('pager', $pager);
            $this->getReviews();
        }
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    /**
     * Customer ID
     *
     * @return Customer ID
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry('customer_Id');
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
     * Get Seller Details
     *
     * @return Seller Details
     */
    public function getUserDetails($userId)
    {
        return $this->customerRepositoryInterface->getById($userId);
    }

    /**
     * Reviewed Or not
     *
     * @return Is Reviewed
     */
    public function isReviewed()
    {
        return $this->reviewsDetails->isReviewed($this->getCustomerId());
    }
    
    /**
     * Get Reviews
     *
     * @return Reviews
     */
    public function getReviews()
    {
    
        if (!$this->reviews) {
            $collection = $this->reviewsCollectionFactory->create();
            $this->reviews = $collection->getCollection()
                            ->addFieldToFilter('seller_id', $this->getSellerId())
                            ->addFieldToFilter('status', 1);
        }
        return $this->reviews;
    }
    
    /**
     * Get Review Count
     *
     * @return Number Of Review
     */
    public function getReviewsCount()
    {
        return $this->reviewsDetails->getReviewsCount($this->getSellerId());
    }
}
