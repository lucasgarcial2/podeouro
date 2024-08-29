<?php
/**
 * Purpletree_Marketplace ProductType
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
namespace Purpletree\Marketplace\Model\Config\Source;

class ProductType implements \Magento\Framework\Option\ArrayInterface
{
    const OPTION_1 = 'Simple';
    const OPTION_2 = 'Configurable';
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::OPTION_1,
                'label' => __('Simple Product')
            ],
            [
                'value' => self::OPTION_2,
                'label' => __('Configurable Product')
            ]
            ];
            return $options;
    }
}
