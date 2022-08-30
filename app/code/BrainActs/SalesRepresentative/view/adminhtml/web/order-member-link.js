/*
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */


define([
    'jquery',
    'Magento_Ui/js/modal/confirm',
    'mage/translate',
    'mage/adminhtml/grid'
], function ($, confirm) {
    'use strict';

    return function (config) {

        var selectedMembers = config.selectedMembers,
            orderMembers = $H(selectedMembers),
            gridJsObject = window[config.gridJsObjectName],
            saveUrl = config.saveUrl;

        //noinspection AmdModulesDependencies
        $('in_order_members').value = Object.toJSON(orderMembers);

        /**
         * Register Product
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerMemberProduct(grid, element, checked)
        {
            if (checked) {
                orderMembers.set(element.value, "");
            } else {
                orderMembers.unset(element.value);
            }
            $('in_order_members').value = Object.toJSON(orderMembers);
            grid.reloadParams = {
                'selected_members[]': orderMembers.keys()
            };
        }

        /**
         * Click on product row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function memberProductRowClick(grid, event)
        {
            var trElement = Event.findElement(event, 'tr'),
                isInput = Event.element(event).tagName === 'INPUT',
                checked = false,
                checkbox = null;

            if (trElement) {
                //noinspection AmdModulesDependencies
                checkbox = Element.getElementsBySelector(trElement, 'input');

                if (checkbox[0]) {
                    checked = isInput ? checkbox[0].checked : !checkbox[0].checked;

                    gridJsObject.setCheckboxChecked(checkbox[0], checked);

                    if (!checked) {
                        confirmToRemove(checkbox, checked);
                    } else {
                        assign(checkbox, checked);
                    }
                }
            }
        }


        /**
         * Remove member from order
         * @param checkbox
         * @param checked
         */
        function confirmToRemove(checkbox, checked)
        {
            confirm({
                title: $.mage.__('Remove member from order'),
                content: $.mage.__('Are you sure you want to remove member from order?'),
                actions: {
                    confirm: function () {

                        $.ajax({
                            showLoader: true,
                            url: saveUrl,
                            data: {"id": checkbox[0].value, "checked": checked},
                            type: "POST",
                            dataType: 'json'
                        }).done(function (data) {
                            salesrep_order_memberJsObject.doFilter();
                        });

                    },
                    cancel: function () {
                        gridJsObject.setCheckboxChecked(checkbox[0], true);
                    },
                    always: function () {
                    }
                }
            });
        }

        /**
         * Assign member to order
         * @param checkbox
         * @param checked
         */
        function assign(checkbox, checked)
        {
            confirm({
                title: $.mage.__('Assign member to order'),
                content: $.mage.__('Are you sure you want to assign member to order?'),
                actions: {
                    confirm: function () {

                        $.ajax({
                            showLoader: true,
                            url: saveUrl,
                            data: {"id": checkbox[0].value, "checked": true},
                            type: "POST",
                            dataType: 'json'
                        }).done(function (data) {
                            salesrep_order_memberJsObject.doFilter();
                        });

                    },
                    cancel: function () {
                        gridJsObject.setCheckboxChecked(checkbox[0], false);
                    },
                    always: function () {
                    }
                }
            });
        }

        gridJsObject.rowClickCallback = memberProductRowClick;
        gridJsObject.checkboxCheckCallback = function (){};

    };
});