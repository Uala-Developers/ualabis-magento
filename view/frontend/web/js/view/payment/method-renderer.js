define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'bis',
                component: 'Uala_Bis/js/view/payment/method-renderer/bis'
            }
        );
        return Component.extend({});
    }
);
