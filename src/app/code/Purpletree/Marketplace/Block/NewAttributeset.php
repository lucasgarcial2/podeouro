<?php
/**
 * Purpletree_Marketplace NewAttributeset
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

class NewAttributeset extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context,
     * @param \Purpletree\Marketplace\Model\AttributesList $attributeRepository,
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param array $data
     */
    public $attributeRepository;
    public $coreRegistry;
    public $attributeSet;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Product\AttributeSet\Options $attributeRepository,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->coreRegistry = $coreRegistry;
        $this->attributeSet = $attributeSet;
        parent::__construct($context, $data);
    }
    public function attributesetlist()
    {
        return $this->attributeRepository->toOptionArray();
    }
        /**
         * Seller ID
         *
         * @return Seller ID
         */
    public function sellerid()
    {
        return $this->coreRegistry->registry('current_customer_id');
    }
    public function attributesetname()
    {
        if (null !== $this->coreRegistry->registry('attributesetid')) {
            $setid = $this->coreRegistry->registry('attributesetid');
            $attributeSetRepository = $this->attributeSet->get($setid);
            $name =  $attributeSetRepository->getAttributeSetName();
            $nameret = explode('_seller_', $name);
            array_pop($nameret);
			return implode('_seller_',$nameret); 
        }
    }
    public function attributesetid()
    {
        if (null !== $this->coreRegistry->registry('attributesetid')) {
            return $this->coreRegistry->registry('attributesetid');
        }
    }
}
