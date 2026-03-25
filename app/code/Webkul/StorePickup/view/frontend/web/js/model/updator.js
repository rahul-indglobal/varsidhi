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
    'ko'
], function (
    $, ko
) {
    'use strict';
    return {
        pickupStores: ko.observableArray([]),
        storeContactInfo: ko.observableArray([]),
        storeAddressInfo: ko.observableArray([]),
        distance: ko.observableArray(''),
        time: ko.observableArray(''),

        setPickupStores: function(data) {
            this.pickupStores(data);
        },

        getPickupStores: function() {
            return this.pickupStores;
        },

        setDistance: function(data) {
            this.distance(data);
        },

        getDistance: function() {
            return this.distance;
        },

        setTime: function(data) {
            this.time(data);
        },

        getTime: function() {
            return this.time;
        },

        setStoreContactInfo: function(data) {
            this.storeContactInfo(data);
        },

        getStoreContactInfo: function() {
            return this.storeContactInfo;
        },

        setStoreAddressInfo: function(data) {
            this.storeAddressInfo(data);
        },

        getStoreAddressInfo: function() {
            return this.storeAddressInfo;
        }
    }
});
