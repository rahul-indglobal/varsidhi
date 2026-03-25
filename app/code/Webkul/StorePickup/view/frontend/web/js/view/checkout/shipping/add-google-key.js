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
    'uiComponent'
], function ($, Component) {
    'use strict';

    return Component.extend({
        initialize: function () {
            var self = this;

            $(document).ready(function () {
                if (window.checkoutConfig.isStorePickupEnabled && window.checkoutConfig.isGoogleMapEnabled) {
                    self.loadScript(window.checkoutConfig.googleKey);
                }
            });

            this._super();
            return this;
        },

        loadScript: function (googleKey) {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = 'https://maps.googleapis.com/maps/api/js?key='+googleKey+"&libraries=places";
            document.body.appendChild(script);
        }
    });
});
