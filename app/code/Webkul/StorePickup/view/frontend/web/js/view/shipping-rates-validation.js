/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_StorePickup
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Webkul_StorePickup/js/model/shipping-rates-validator',
        'Webkul_StorePickup/js/model/shipping-rates-validation-rules'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        storePickupShippingRatesValidator,
        storePickupShippingRatesValidationRules
    ) {
        'use strict';
        defaultShippingRatesValidator.registerValidator('storepickup', storePickupShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('storepickup', storePickupShippingRatesValidationRules);

        return Component;
    }
);
