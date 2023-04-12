define(
    [
        'jquery',
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function ($,Component,urlBuilder) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Uala_Bis/payment/bis',
                redirectAfterPlaceOrder: false
            },
            afterPlaceOrder: function (url) {
                window.location.replace(urlBuilder.build('bis/payment/redirect/'));
            },
            getMessage: function(){
                return window.checkoutConfig.payment.bis.message;
            }
        });
    }
);