<?php
/**
 * Purpletree_Marketplace NewAttribute
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

class NewAttribute extends \Magento\Framework\View\Element\Template
{

    public $attributeRepository;
    public $coreRegistry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Product\AttributeSet\Options $attributeRepository,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->coreRegistry = $coreRegistry;
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
}
