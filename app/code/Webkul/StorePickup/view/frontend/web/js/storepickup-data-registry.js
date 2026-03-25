/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_StorePickup
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

require([], function () {
    'use strict';

    var cache = [];

    return {
        /**
         * @param {String} addressKey
         * @return {*}
         */
        get: function (addressKey) {
            if (cache[addressKey]) {
                return cache[addressKey];
            }

            return false;
        },

        /**
         * @param {String} addressKey
         * @param {*} data
         */
        set: function (addressKey, data) {
            cache[addressKey] = data;
        }
    };
});
