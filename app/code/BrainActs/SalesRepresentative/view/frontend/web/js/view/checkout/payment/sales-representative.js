/*
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiComponent',
    'ko',
    'jquery',
    'Magento_Ui/js/modal/alert'
], function (Component, ko, $, alert) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'BrainActs_SalesRepresentative/checkout/payment/sales-representative'
        },

        config: {

            availableMembers: ko.observableArray(window.checkoutConfig.salesrep.members),

            isEnabled: function () {
                return window.checkoutConfig.salesrep.isEnabled;
            },

            updateSalesRepresentative: function (data, event) {
                var select = $('#salesrep_member');
                var selectedMember = select.val();

                $.ajax({
                    url: window.checkoutConfig.salesrep.checkoutUrl  + selectedMember,
                    type: 'put',
                    dataType: 'json',
                    context: this,
                    beforeSend: function () {
                        select.attr('disabled', 'disabled');
                    },
                    complete: function () {
                        select.attr('disabled', null);
                    }
                })
                    .done(function (response) {
                        if (response.success) {
                            //callback.call(this, elem, response);
                        } else {
                            var msg = response.error_message;

                            if (msg) {
                                alert({
                                    content: $.mage.__(msg)
                                });
                            }
                        }
                    })
                    .fail(function (error) {
                        console.log(JSON.stringify(error));
                    });
            },

            /**
             * @param {Object} data - post data for ajax call
             * @param {Object} elem - element that initiated the event
             * @param {Function} callback - callback method to execute after AJAX success
             */
            _ajax: function (data, elem, callback) {
                $.extend(data, {
                    'form_key': $.mage.cookies.get('form_key')
                });


            },

            _setupSRMemberAfter: function (elem) {

            },

            _getResponsibleUrl: function () {
                return window.checkoutConfig.salesrep.checkoutUrl;
            }
        }


    });
});
