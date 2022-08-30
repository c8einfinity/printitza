/**
 * Created by Ajay on 27-09-2017.
 */
define([
    'jquery'
], function ($) {
    "use strict";

    return function (config, element) {

        $(element).click(function () {
            debugger;
            var form = $("#product_addtocart_form");
            var action = config.action;
            console.log(action);
            // change form action
            var baseUrl = form.attr('action');

            form.attr('action', action);

            form.trigger('submit');

            // set form action back
            form.attr('action', baseUrl);

            return false;
        });
    }
});