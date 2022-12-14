/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true*/
/*global alert*/
/**
 * Checkout adapter for customer data storage
 */
define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, storage) {
    'use strict';

    var cacheKey = 'checkout-data';

    var getData = function () {
        return storage.get(cacheKey)();
    };

    var saveData = function (checkoutData) {
        storage.set(cacheKey, checkoutData);
    };

    if ($.isEmptyObject(getData())) {
        var checkoutData = {
            'selectedShippingAddress': null,
            'shippingAddressFromData' : null,
            'newCustomerShippingAddress' : null,
            'selectedShippingRate' : null,
            'selectedPaymentMethod' : null,
            'selectedBillingAddress' : null,
            'billingAddressFormData' : null,
            'newCustomerBillingAddress' : null
        };
        saveData(checkoutData);
    }

    return {
        setSelectedShippingAddress: function (data) {
            var obj = getData();
            obj.selectedShippingAddress = data;
            saveData(obj);
        },

        getSelectedShippingAddress: function () {
            return getData().selectedShippingAddress;
        },

        setShippingAddressFromData: function (data) {
            var obj = getData();
            obj.shippingAddressFromData = data;
            saveData(obj);
        },

        getShippingAddressFromData: function () {
            return getData().shippingAddressFromData;
        },

        setNewCustomerShippingAddress: function (data) {
            var obj = getData();
            obj.newCustomerShippingAddress = data;
            saveData(obj);
        },

        getNewCustomerShippingAddress: function () {
            return getData().newCustomerShippingAddress;
        },

        setSelectedShippingRate: function (data) {
            $("div#onepage-checkout-shipping-method-additional-load").hide();

            /*if ($('#s_method_storepickup_storepickup').is(':checked')) {
                $("div#onepage-checkout-shipping-method-additional-load").show();
                $("div#onepage-checkout-shipping-method-additional-load").css("width",350);
            } else {
                $("div#onepage-checkout-shipping-method-additional-load").hide();
            }*/
            if ($("input[value='storepickup_storepickup']").is(':checked')) {
                $("div#onepage-checkout-shipping-method-additional-load").show();
                $("div#onepage-checkout-shipping-method-additional-load").css("width",350);
            } else {
                $("div#onepage-checkout-shipping-method-additional-load").hide();
            }
            var obj = getData();
            obj.selectedShippingRate = data;
            saveData(obj);
        },

        getSelectedShippingRate: function () {
            $("div#onepage-checkout-shipping-method-additional-load").hide();
            /*if ($('#s_method_storepickup_storepickup').length > 0) {
                if ($('#s_method_storepickup_storepickup').is(':checked')) {
                    $("div#onepage-checkout-shipping-method-additional-load").show();
                    $("div#onepage-checkout-shipping-method-additional-load").css("width",350);
                } else {
                    $("div#onepage-checkout-shipping-method-additional-load").hide();
                }
            } else {
                $("div#onepage-checkout-shipping-method-additional-load").hide();
            }*/

            if ($("input[value='storepickup_storepickup']").length > 0) {
                if ($("input[value='storepickup_storepickup']").is(':checked')) {
                    $("div#onepage-checkout-shipping-method-additional-load").show();
                    $("div#onepage-checkout-shipping-method-additional-load").css("width",350);
                } else {
                    $("div#onepage-checkout-shipping-method-additional-load").hide();
                }
            } else {
                $("div#onepage-checkout-shipping-method-additional-load").hide();
            }
            
            return getData().selectedShippingRate;
        },

        setSelectedPaymentMethod: function (data) {
            var obj = getData();
            obj.selectedPaymentMethod = data;
            saveData(obj);
        },

        getSelectedPaymentMethod: function () {
            return getData().selectedPaymentMethod;
        },

        setSelectedBillingAddress: function (data) {
            var obj = getData();
            obj.selectedBillingAddress = data;
            saveData(obj);
        },

        getSelectedBillingAddress: function () {
            return getData().selectedBillingAddress;
        },

        setBillingAddressFromData: function (data) {
            var obj = getData();
            obj.billingAddressFromData = data;
            saveData(obj);
        },

        getBillingAddressFromData: function () {
            return getData().billingAddressFromData;
        },

        setNewCustomerBillingAddress: function (data) {
            var obj = getData();
            obj.newCustomerBillingAddress = data;
            saveData(obj);
        },

        getNewCustomerBillingAddress: function () {
            return getData().newCustomerBillingAddress;
        },

        getValidatedEmailValue: function () {
            var obj = getData();
            return (obj.validatedEmailValue) ? obj.validatedEmailValue : '';
        },

        setValidatedEmailValue: function (email) {
            var obj = getData();
            obj.validatedEmailValue = email;
            saveData(obj);
        },

        getInputFieldEmailValue: function () {
            var obj = getData();
            return (obj.inputFieldEmailValue) ? obj.inputFieldEmailValue : '';
        },

        /**
         * Pulling the checked email value from persistence storage
         *
         * @return {*}
         */
        getCheckedEmailValue: function () {
            var obj = getData();

            return obj.checkedEmailValue ? obj.checkedEmailValue : '';
        },

        setInputFieldEmailValue: function (email) {
            var obj = getData();
            obj.inputFieldEmailValue = email;
            saveData(obj);
        },

        /**
         * Pulling the checked email value from persistence storage
         *
         * @return {*}
         */
        getCheckedEmailValue: function () {
            var obj = getData();

            return obj.checkedEmailValue ? obj.checkedEmailValue : '';
        },

        /**
         * Setting the checked email value pulled from persistence storage
         *
         * @param {String} email
         */
        setCheckedEmailValue: function (email) {
            var obj = getData();

            obj.checkedEmailValue = email;
            saveData(obj);
        }
    }
});
