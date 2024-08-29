<?php
/**
* Purpletree_Marketplace Tablerate
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
namespace Purpletree\Marketplace\Model\Config\Source;

/**
 * @api
 * @since 100.0.2
 */
class Tablerate implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Purpletree\Marketplace\Model\Carrier\Tablerate
     */
    protected $_carrierTablerate;

    /**
     * @param \Purpletree\Marketplace\Model\Carrier\Tablerate $carrierTablerate
     */
    public function __construct(\Purpletree\Marketplace\Model\Carrier\Tablerate $carrierTablerate)
    {
        $this->_carrierTablerate = $carrierTablerate;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $arr = [];
        foreach ($this->_carrierTablerate->getCode('condition_name') as $k => $v) {
            $arr[] = ['value' => $k, 'label' => $v];
        }
        return $arr;
    }
}
