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
    'jquery',
    'mageUtils',
    '../pts-shipping-rates-validation-rules/tablerate',
    'mage/translate'
], function ($, utils, validationRules, $t) {
    'use strict';

    return {
        validationErrors: [],

        /**
         * @param {Object} address
         * @return {Boolean}
         */
        validate: function (address) {
            var self = this;

            this.validationErrors = [];
            $.each(validationRules.getRules(), function (field, rule) {
                var message, regionFields;

                if (rule.required && utils.isEmpty(address[field])) {
                    message = $t('Field ') + field + $t(' is required.');
                    regionFields = ['region', 'region_id', 'region_id_input'];

                    if (
                        $.inArray(field, regionFields) === -1 ||
                        utils.isEmpty(address.region) && utils.isEmpty(address['region_id'])
                    ) {
                        self.validationErrors.push(message);
                    }
                }
            });

            return !this.validationErrors.length;
        }
    };
});
