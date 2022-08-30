/**
 * Created by Ajay on 01-08-2017.
 */
/**
 * Copyright © 2017 DesignNBuy. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/element/select',
    'uiRegistry'
], function (jQuery, Select, uiRegistry) {
    'use strict';

    return Select.extend({
        initialize: function () {
            this._super();
            var  provider = uiRegistry.get(this.provider),
                data = provider.data;
            console.log(data.printable_colors);

            this.updateFieldSet(data.printable_colors, 'printable_colors');
            return this;
        },
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            this.updateFieldSet(value, this.index);
            return this._super();
        },
        updateFieldSet: function (value, index) {
            var dependentField = uiRegistry.get('visibleByPrintableColors = ' + value);
            if (typeof dependentField != 'undefined') {
                var dependentFields = uiRegistry.filter('dependsOnPrintableColors = ' + index);
                jQuery(dependentFields).each(function (e, t) {
                    t.visible(true);
                });
                // dependentField.visible(true);
            } else {
                var dependentFields = uiRegistry.filter('dependsOnPrintableColors = ' + index);
                jQuery(dependentFields).each(function (e, t) {
                    t.visible(false);
                });
            }
        }


    });
});
