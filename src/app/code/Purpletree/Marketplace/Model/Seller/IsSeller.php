<?php

/**
 * Purpletree_Marketplace Isseller
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Purpletree\Marketplace\Model\Seller;

class IsSeller extends \Magento\Eav\Model\Entity\Attribute\Source\Boolean
{
    protected $options;
     /**
     * Value of 'Use Config' option
     */
    const VALUE_USE_CONFIG = '';

    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->options) {
            $this->options = [
                ['label' => __('Yes'), 'value' => static::VALUE_YES],
                ['label' => __('No'), 'value' => static::VALUE_NO],
            ];
        }
        return $this->options;
    }
}
