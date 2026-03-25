/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_StorePickup
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Webkul_StorePickup/js/view/shipping': true
            }
        }
    },

    map: {
        '*': {
            'Magento_Checkout/js/model/shipping-rate-processor/new-address': 'Webkul_StorePickup/js/model/shipping-rate-processor/new-address',
            'Magento_Checkout/js/model/shipping-rate-processor/customer-address': 'Webkul_StorePickup/js/model/shipping-rate-processor/customer-address',
            'Magento_Checkout/js/checkout-data': 'Webkul_StorePickup/js/checkout-data',
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': 'Webkul_StorePickup/js/model/shipping-save-processor/payload-extender',
            'detailswidgetjs': 'Webkul_StorePickup/js/widget/detailswidgetjs'
        }
    }
};
