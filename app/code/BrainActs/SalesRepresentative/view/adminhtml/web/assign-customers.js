/*
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

/* global $, $H */

define([
    'mage/adminhtml/grid'
], function () {
    'use strict';

    return function (config) {

        //noinspection JSUnresolvedVariable
        var selectedCustomers = config.selectedCustomers,
            memberCustomers = $H(selectedCustomers),
            gridJsObject = window[config.gridJsObjectName];

        //noinspection AmdModulesDependencies
        $('in_member_customers').value = Object.toJSON(memberCustomers);

        /**
         * Register Customer
         *
         * @param {Object} grid
         * @param {Object} element
         * @param {Boolean} checked
         */
        function registerMemberCustomer(grid, element, checked)
        {

            if (checked) {
                memberCustomers.set(element.value, "");
            } else {
                memberCustomers.unset(element.value);
            }


            $('in_member_customers').value = Object.toJSON(memberCustomers);
            grid.reloadParams = {
                'selected_customers[]': memberCustomers.keys()
            };
        }

        /**
         * Click on customer row
         *
         * @param {Object} grid
         * @param {String} event
         */
        function memberCustomerRowClick(grid, event)
        {
            //noinspection AmdModulesDependencies
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
                }
            }
        }


        gridJsObject.rowClickCallback = memberCustomerRowClick;
        gridJsObject.checkboxCheckCallback = registerMemberCustomer;

    };
});
