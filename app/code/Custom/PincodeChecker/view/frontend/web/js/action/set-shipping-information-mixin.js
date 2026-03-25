require([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-service',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address',
], function ($, wrapper, quote, shippingService, rateRegistry, customerAddressProcessor, newAddressProcessor) {

    $(document).on('change',"[name='country']",function(){
      alert("sdfsdfdf");
    });


});