/**
* Purpletree_Marketplace system
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

define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    '../../model/pts-shipping-rates-validator/tablerate',
    '../../model/pts-shipping-rates-validation-rules/tablerate'
], function (
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    tablerateShippingRatesValidator,
    tablerateShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('purpletreetablerate', tablerateShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('purpletreetablerate', tablerateShippingRatesValidationRules);

    return Component; 
});
