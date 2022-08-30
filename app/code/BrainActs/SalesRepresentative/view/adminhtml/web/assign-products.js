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

        var selectedProducts = config.selectedProducts,
            memberProducts = $H(selectedProducts),
            gridJsObject = window[config.gridJsObjectName];

        //noinspection AmdModulesDependencies
        $('in_member_products').value = Object.toJSON(memberProducts);

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
                memberProducts.set(element.value, "");
            } else {
                memberProducts.unset(element.value);
            }
            $('in_member_products').value = Object.toJSON(memberProducts);
            grid.reloadParams = {
                'selected_products[]': memberProducts.keys()
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


        gridJsObject.rowClickCallback = memberProductRowClick;
        gridJsObject.checkboxCheckCallback = registerMemberProduct;

    };
});
