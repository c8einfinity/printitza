/**
 * Created by Ajay on 27-09-2017.
 */
define([
    'jquery'
], function ($) {
    "use strict";

    return function (config, element) {

        $(element).click(function () {
            var form = $("#product_addtocart_form");
            var action = config.action;
            console.log(action);
            // change form action
            var baseUrl = form.attr('action'),
                buyNowUrl = baseUrl.replace('checkout/cart/add', 'canvas/index/index');

            form.attr('action', action);

            form.trigger('submit');

            // set form action back
            form.attr('action', baseUrl);

            return false;
        });
        
        $('.related.template-container .product-items .product-image-photo').click(function() {
            jQuery(this).parents(".product-item-info").find(".product-item-details .actions-primary button.tocart.template").click();
        });
    }
});