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
    'ko',
    'Webkul_StorePickup/js/model/updator',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/checkout-data',
    'Magento_Ui/js/modal/modal'
], function (
    $, ko, updator, quote, selectShippingMethodAction, checkoutData, modal
) {
    'use strict';

    return function (Shipping) {
        return Shipping.extend({
            pickupStores: ko.observableArray([]),
            pickupStoreError: ko.observable(false),

            storeContactInfo: ko.observableArray([]),
            storeAddressInfo: ko.observableArray([]),

            distance: ko.observable(''),
            time: ko.observable(''),

            isGoogleMapEnabled: window.checkoutConfig.isGoogleMapEnabled,

            initialize: function () {
                var self = this;

                updator.pickupStores.subscribe(function (newValue) {
                    if (newValue.length == 0) {
                        $("[name='pickupstores_radio']").prop('disabled', true);
                    } else {
                        $("[name='pickupstores_radio']").prop('disabled', false);
                    }
                    self.pickupStores(newValue);
                    self.pickupStoreError(false);
                });

                updator.storeContactInfo.subscribe(function (newValue) {
                    self.storeContactInfo(newValue);
                });

                updator.storeAddressInfo.subscribe(function (newValue) {
                    self.storeAddressInfo(newValue);
                });

                updator.distance.subscribe(function (newValue) {
                    self.distance(newValue);
                });

                updator.time.subscribe(function (newValue) {
                    self.time(newValue);
                });

                this._super();
                return this;
            },

            validateShippingInformation: function () {
                if (window.checkoutConfig.isStorePickupEnabled == 0) {
                    return this._super();
                }

                if (quote.shippingMethod() && quote.shippingMethod()['carrier_code'] == 'storepickup') {
                    if (this.pickupStores().length == 0) {
                        quote.shippingMethod(null);
                    } else {
                        if ($("input[name='pickupstores_radio']:checked").length == 0) {
                            $("#pickup-store-error-section").css('display', 'block');
                            this.pickupStoreError(true);
                            return false;
                        }
                    }
                }

                if (checkoutData.getSelectedShippingRate() == 'storepickup_storepickup') {
                    quote.pickupStore = checkoutData.getSelectedPickupStore();
                } else {
                    quote.pickupStore = null;
                }

                return this._super();
            },

            /**
             * @param {Object} shippingMethod
             * @return {Boolean}
             */
            selectShippingMethod: function (shippingMethod) {
                selectShippingMethodAction(shippingMethod);
                checkoutData.setSelectedShippingRate(shippingMethod['carrier_code'] + '_' + shippingMethod['method_code']);

                if (shippingMethod['carrier_code'] + '_' + shippingMethod['method_code'] == 'storepickup_storepickup') {
                    $("[name='pickupstores_radio']").prop('disabled', false);
                } else {
                    $("#pickup-store-error-section").css('display', 'none');
                    $("[name='pickupstores_radio']").prop('disabled', true);
                }

                return true;
            },

            isChecked: function (pickupStore) {
                if (checkoutData.getSelectedPickupStore() == pickupStore) {
                    return 'checked';
                }

                return false;
            },

            /**
             * @param {number} id
             * @return {void}
             */
            selectPickupStore: function (id) {
                checkoutData.setSelectedPickupStore(id);
                $("#"+id).prop('checked', true);
                $("input[value='storepickup_storepickup'].radio").click();
                $("#pickup-store-error-section").css('display', 'none');
                return true;
            },

            initModal: function () {
                var options = {
                    type: 'popup',
                    title: 'Store Details',
                    responsive: true,
                    innerScroll: true,
                    buttons: [{
                        text: $.mage.__('Close'),
                        class: '',
                        click: function () {
                            this.closeModal();
                        }
                    }]
                };

                var popup = modal(options, $('#popup-modal'));
            },

            openDetails: function (data) {
                var contactDetails = [
                    {name: $.mage.__('Person Name'), value: data.store.stores_details.person_name},
                    {name: $.mage.__('Email'), value: data.store.stores_details.email},
                    {name: $.mage.__('Phone'), value: data.store.stores_details.mobile},
                    {name: $.mage.__('Fax'), value: data.store.stores_details.fax}
                ];

                updator.setStoreContactInfo(contactDetails);

                var storeAddress = [
                    {name: $.mage.__('Country'), value: data.store.stores_details.country_id},
                    {name: $.mage.__('State'), value: data.store.stores_details.region},
                    {name: $.mage.__('City'), value: data.store.stores_details.city},
                    {name: $.mage.__('Street'), value: data.store.stores_details.street},
                    {name: $.mage.__('Postcode'), value: data.store.stores_details.postcode}
                ];

                updator.setStoreAddressInfo(storeAddress);

                var directionsService = new google.maps.DirectionsService();
                var directionsDisplay = new google.maps.DirectionsRenderer();

                var mapProp = {
                    center: new google.maps.LatLng(data.store.latitude, data.store.longitude),
                    zoom: 10,
                    mapType: google.maps.MapTypeId.ROADMAP
                };

                var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

                var addr = quote.shippingAddress();
                var qry = addr.street.join(', ')+", ";
                qry += addr.city+", "+addr.region+", "+addr.countryId+", "+addr.postcode;

                var start = new google.maps.LatLng(data.dest_latitude, data.dest_longitude);
                var end = new google.maps.LatLng(data.store.latitude, data.store.longitude);

                var bounds = new google.maps.LatLngBounds();
                bounds.extend(start);
                bounds.extend(end);
                map.fitBounds(bounds);

                var request = {
                    origin: {query: qry},
                    destination: end,
                    travelMode: google.maps.TravelMode.DRIVING
                };

                directionsService.route(request, function(response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        updator.setDistance(data.distance+" "+data.distance_unit);
                        updator.setTime(response.routes[0].legs[0].duration.text);
                        directionsDisplay.setDirections(response);
                        directionsDisplay.setMap(map);
                    } else {
                        console.log("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
                    }
                });

                $("#popup-modal").modal("openModal");
            }
        });
    }
});
