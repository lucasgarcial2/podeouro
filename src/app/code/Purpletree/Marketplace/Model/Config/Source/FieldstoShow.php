<?php

/**
 * Purpletree_Marketplace FieldstoShow
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
namespace Purpletree\Marketplace\Model\Config\Source;

/**
 * Used in creating options for getting product type value
 *
 */
class FieldstoShow
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
        ['value' => 'email', 'label'=>__('Store Email')],
        ['value' => 'phone', 'label'=>__('Store Phone')],
        ['value' => 'address', 'label'=>__('Store Address')]
        ];
    }
}
