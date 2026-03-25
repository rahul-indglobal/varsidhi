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
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/error-processor',
    'Webkul_StorePickup/js/model/updator',
    'Webkul_StorePickup/js/storepickup-data-registry'
], function (resourceUrlManager, quote, storage, shippingService, rateRegistry, errorProcessor, updator, storePickupRegistry) {
    'use strict';
    var loader1, loader2;
    return {
        /**
         * @param {Object} address
         */
        getRates: function (address) {
            var cache;
            var storePickupCache;

            shippingService.isLoading(true);
            cache = rateRegistry.get(address.getKey());

            if (cache) {
                shippingService.setShippingRates(cache);

                storePickupCache = storePickupRegistry.get(address.getKey());
                if (storePickupCache) {
                    updator.setPickupStores(storePickupCache);
                } else {
                    this.findPickupStore(address);
                }

                loader1 = true;
                if (loader2) {
                    shippingService.isLoading(false);
                }
            } else {
                storage.post(
                    resourceUrlManager.getUrlForEstimationShippingMethodsByAddressId(),
                    JSON.stringify({
                        addressId: address.customerAddressId
                    }),
                    false
                ).done(function (result) {
                    rateRegistry.set(address.getKey(), result);
                    shippingService.setShippingRates(result);
                }).fail(function (response) {
                    shippingService.setShippingRates([]);
                    errorProcessor.process(response);
                }).always(function () {
                    loader1 = true;
                    if (loader2) {
                        shippingService.isLoading(false);
                    }
                });

                this.findPickupStore(address);
            }
        },

        findPickupStore: function (address) {
            var serviceUrl = "storepickup/stores/findnearestpickupstores";
            address.country_id = address.countryId;
            var payload = JSON.stringify({
                address: address
            });

            storage.post(
                serviceUrl, payload, false
            ).done(function (response) {
                if (response.success) {
                    storePickupRegistry.set(address.getKey(), response.result);
                    if (response.result.length) {
                        updator.setPickupStores(response.result);
                    } else {
                        updator.setPickupStores([]);
                    }
                }
            }).fail(function (response) {
                errorProcessor.process(response);
            }).always(function () {
                loader2 = true;
                if (loader1) {
                    shippingService.isLoading(false);
                }
            });
        }
    };
});
