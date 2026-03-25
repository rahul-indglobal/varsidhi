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
    'jquery',
    'uiComponent',
    'ko',
    'mage/storage',
    'Magento_Ui/js/modal/confirm'
], function ($, Component, ko, storage, confirmation) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Webkul_StorePickup/checkout/shipping/storeslist'
        },

        initialize: function () {
            this._super();
            return this;
        },

        initObservable: function () {
            return this;
        },

        isChecked: function () {
            return true;
        }
    });
});
