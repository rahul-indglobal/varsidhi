/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require([
    'jquery'
], function ($) {
    'use strict';
    return function (quote) {
        quote.isVirtual = function() {
            return false;
        };
        return quote;
    }
});
