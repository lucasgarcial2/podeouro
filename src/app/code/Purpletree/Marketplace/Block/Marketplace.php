<?php
/**
 * Purpletree_Marketplace Marketplace
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
 
class Marketplace extends \Magento\Framework\View\Element\Template
{

    /**
     * @param  \Magento\Framework\View\Element\Template\Context $context
     * @param  \Purpletree\Marketplace\Model\AttributesList $attributesList
     */
    public $attributesList;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Purpletree\Marketplace\Model\AttributesList $attributesList
    ) {
    
        $this->attributesList = $attributesList;
        parent::__construct($context);
    }
    
    public function getWelcomeTxt()
    {
        $result = $this->attributesList->getAttributes();
        return $result;
    }
}
