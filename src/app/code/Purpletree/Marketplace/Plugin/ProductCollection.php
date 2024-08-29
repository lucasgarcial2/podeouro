<?php
/**
 * Purpletree_Marketplace ProductCollection
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

namespace Purpletree\Marketplace\Plugin;

class ProductCollection
{
	public $_registry;
	public function afterIsEnabledFlat(\Magento\Catalog\Model\ResourceModel\Product\Collection $subject,$result)
	{
		
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$this->_registry = $objectManager->get('\Magento\Framework\Registry');
		if($this->_registry->registry('seller_products')) {
			$result = false;
			return false;
		}	
	}
}
