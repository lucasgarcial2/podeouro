<?php

/**
 * Purpletree_Marketplace Sellerorder
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
namespace Purpletree\Marketplace\Model;

class Sellerorder extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'purpletree_marketplace_sellerorder';

    protected $_cacheTag = 'purpletree_marketplace_sellerorder';

    protected $_eventPrefix = 'purpletree_marketplace_sellerorder';

    protected function _construct()
    {
        $this->_init('Purpletree\Marketplace\Model\ResourceModel\Sellerorder');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
