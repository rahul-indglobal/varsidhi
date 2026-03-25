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
         * Get shipping rates for specified address.
         * @param {Object} address
         */
        getRates: function (address) {
            var cache, serviceUrl, payload, storePickupCache;

            shippingService.isLoading(true);
            cache = rateRegistry.get(address.getCacheKey());
            serviceUrl = resourceUrlManager.getUrlForEstimationShippingMethodsForNewAddress(quote);
            payload = JSON.stringify({
                    address: {
                        'street': address.street,
                        'city': address.city,
                        'region_id': address.regionId,
                        'region': address.region,
                        'country_id': address.countryId,
                        'postcode': address.postcode,
                        'email': address.email,
                        'customer_id': address.customerId,
                        'firstname': address.firstname,
                        'lastname': address.lastname,
                        'middlename': address.middlename,
                        'prefix': address.prefix,
                        'suffix': address.suffix,
                        'vat_id': address.vatId,
                        'company': address.company,
                        'telephone': address.telephone,
                        'fax': address.fax,
                        'custom_attributes': address.customAttributes,
                        'save_in_address_book': address.saveInAddressBook
                    }
                }
            );

            if (cache) {
                shippingService.setShippingRates(cache);

                storePickupCache = storePickupRegistry.get(address.getCacheKey());
                if (storePickupCache) {
                    updator.setPickupStores(storePickupCache);
                } else {
                    this.findPickupStore(payload, address);
                }

                loader1 = true;
                if (loader2) {
                    shippingService.isLoading(false);
                }
            } else {
                storage.post(
                    serviceUrl, payload, false
                ).done(function (result) {
                    rateRegistry.set(address.getCacheKey(), result);
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

                this.findPickupStore(payload, address);
            }
        },

        findPickupStore: function (payload, address) {
            var serviceUrl = "storepickup/stores/findnearestpickupstores";
            storage.post(
                serviceUrl, payload, false
            ).done(function (response) {
                if (response.success) {
                    storePickupRegistry.set(address.getCacheKey(), response.result);
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
