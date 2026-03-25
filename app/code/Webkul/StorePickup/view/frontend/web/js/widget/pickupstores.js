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
    "jquery",
    'uiComponent',
    'Magento_Ui/js/modal/alert',
    'mage/storage',
    'ko'
],function ($, Component, alert, storage, ko) {
    "use strict";

    var latitude, longitude, config;
    var mapData;
    return Component.extend({
        isCurrentLocationSet: ko.observable(false),
        isShowDirection: ko.observable(false),
        activePickupStoreList: ko.observable(false),

        defaults: {
            template: 'Webkul_StorePickup/widget/pickup-stores-list'
        },

        pickupStores: ko.observableArray([]),

        initialize: function (config) {
            this._super();
            this.config = config;
            var self = this;

            $(document).ready(function() {
                if (config.isAddressSearchEnabled) {
                    self.initPlaceSearch();
                }

                $("#address_location").change (function () {
                    $("#address_location_content").slideDown();
                    $("#manual_location_content").slideUp();
                });

                $("#manual_location").change (function () {
                    $("#manual_location_content").slideDown();
                    $("#address_location_content").slideUp();
                });

                $("#set-lat-lng-btn").click(function () {
                    self.renderByLatLong();
                });

                $("#find-n-set-lat-lng-btn").click(function () {
                    if (config.isSecure) {
                        self.getLocation();
                    }
                });
            });
        },

        renderByLatLong: function () {
            var self = this;
            var lat = Number($("#latitude").val());
            var lng = Number($("#longitude").val());

            if (lat && lng) {
                latitude = lat;
                longitude = lng;

                var location = $.mage.__('Latitude - ')+latitude;
                location += $.mage.__(", Longitude - ")+longitude;
                $(".show-set-location").text(location);
                self.getPickupStoresList();
            } else {
                alert({
                    title: $.mage.__('Store Pickup'),
                    content: $.mage.__('Please provide latitude or longitude in proper format.')
                });
            }
        },

        showAllPickupStores: function () {
            var self = this;
            if (mapData != "all") {
                self.isShowDirection(false);
                self.activePickupStoreList(0);
                var locations = [];
                var pickupStores = this.pickupStores();

                pickupStores.forEach (function (store, index) {
                    var temp = [
                        store.store.name,
                        store.dest_latitude,
                        store.dest_longitude,
                        index
                    ];

                    locations.push(temp);
                });

                if (locations.length) {
                    var originLatitude = pickupStores[0].origin_latitude;
                    var originLongitude = pickupStores[0].origin_longitude;

                    var map = new google.maps.Map(document.getElementById('googleMap'), {
                        zoom: 10,
                        center: new google.maps.LatLng(originLatitude, originLongitude),
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    });

                    var infowindow = new google.maps.InfoWindow();
                    var bounds = new google.maps.LatLngBounds();
                    var marker, i;

                    for (i = 0; i < locations.length; i++) {
                        marker = new google.maps.Marker({
                            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                            map: map
                        });

                        if (locations.length > 1) {
                            bounds.extend(marker.position);
                        }

                        google.maps.event.addListener(marker, 'click', (function(marker, i) {
                            return function() {
                                infowindow.setContent(locations[i][0]);
                                infowindow.open(map, marker);
                            }
                        })(marker, i));
                    }

                    if (locations.length > 1) {
                        map.fitBounds(bounds);
                    }
                }
                mapData = "all";
            }

            return true;
        },

        showPickupStoreDirection: function () {
            var self = this;
            if (mapData != 'direction') {
                self.isShowDirection(true);
                mapData = "direction";
            }

            return true;
        },

        getLocation: function () {
            var self = this;
            $('body').trigger('processStart');
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    $("#latitude").val(position.coords.latitude);
                    $("#longitude").val(position.coords.longitude);
                    self.renderByLatLong();
                    $('body').trigger('processStop');
                }, function error (msg) {
                    $('body').trigger('processStop');
                    alert({
                        title: $.mage.__('Store Pickup'),
                        content: $.mage.__('Some issue occurring in getting latitude longitude.')
                    });
                }, {
                    enableHighAccuracy: true
                });
            } else {
                $('body').trigger('processStop');
                alert({
                    title: $.mage.__('Store Pickup'),
                    content: $.mage.__('Geo Location is supported by your browser.')
                });
            }
        },

        initPlaceSearch: function () {
            var self = this;
            var input = document.getElementById('autocomplete');
            var autocomplete = new google.maps.places.Autocomplete(input);
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                var place = autocomplete.getPlace();
                latitude = place.geometry.location.lat();
                longitude = place.geometry.location.lng();
                var location = $.mage.__('Latitude - ')+latitude;
                location += $.mage.__(", Longitude - ")+longitude;
                $(".show-set-location").text(location);
                self.getPickupStoresList();
            });
        },

        getPickupStoresList: function () {
            var self = this;
            var range = self.config.getWithinRange;
            $('body').trigger('processStart');
            storage.post(
                this.config.baseUrl+"storepickup/stores/findnearestpickupstoreslistbyrange",
                JSON.stringify({originLat: latitude, originLng: longitude, range: range})
            ).done(function (response) {
                if (response.success) {
                    self.pickupStores.removeAll();
                    response.result.forEach (function (store) {
                        self.pickupStores.push(store);
                    });

                    if (self.pickupStores().length) {
                        self.isCurrentLocationSet(true);

                        if ($("input[name='map_chooser']:checked").length == 0) {
                            $("input[name='map_chooser']:eq(0)").click();
                        }
                    } else {
                        self.isCurrentLocationSet(false);
                        alert({
                            title: $.mage.__('Store Pickup'),
                            content: $.mage.__('Pickup stores not found at this location.')
                        });
                    }

                    if (mapData == 'all') {
                        mapData = 'direction';
                        self.showAllPickupStores();
                    }
                }

                $('body').trigger('processStop');
            });
        },

        showMap: function (store) {
            var self = this;
            if (self.activePickupStoreList() != store.store.entity_id) {
                self.activePickupStoreList(store.store.entity_id);

                var directionsService = new google.maps.DirectionsService();
                var directionsDisplay = new google.maps.DirectionsRenderer();

                var mapProp = {
                    center: new google.maps.LatLng(store.dest_latitude, store.dest_longitude),
                    zoom: 10,
                    mapType: google.maps.MapTypeId.ROADMAP
                };

                var map = new google.maps.Map(document.getElementById("googleMap"), mapProp);
                var start = new google.maps.LatLng(store.origin_latitude, store.origin_longitude);
                var end = new google.maps.LatLng(store.dest_latitude, store.dest_longitude);

                var bounds = new google.maps.LatLngBounds();
                bounds.extend(start);
                bounds.extend(end);
                map.fitBounds(bounds);

                var request = {
                    origin: start,
                    destination: end,
                    travelMode: google.maps.TravelMode.DRIVING
                };

                directionsService.route(request, function(response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);
                        directionsDisplay.setMap(map);
                    } else {
                        console.log("Directions Request from " + start.toUrlValue(6) + " to " + end.toUrlValue(6) + " failed: " + status);
                    }
                });
            }
        }

    });
});
