/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_StorePickup
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

require([
    'Magento_Checkout/js/model/quote'
], function (quote) {
    'use strict';

    return function (payload) {
        payload.addressInformation['extension_attributes'] = {};
        payload.addressInformation['extension_attributes']['pickup_store'] = quote.pickupStore;
        return payload;
    };
});
