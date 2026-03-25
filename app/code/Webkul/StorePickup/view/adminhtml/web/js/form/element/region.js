/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_StorePickup
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

define([
    'Magento_Ui/js/form/element/region'
], function (Region) {
    'use strict';

    return Region.extend({
        defaults: {
            regionScope: 'data.details_data.region'
        },

        /**
         * Set region to source form
         * @param {String} value - region
         */
        setDifferedFromDefault: function (value) {
            this._super();

            if (parseFloat(value)) {
                this.source.set(this.regionScope, this.indexedOptions[value].label);
            }
        }
    });
});
