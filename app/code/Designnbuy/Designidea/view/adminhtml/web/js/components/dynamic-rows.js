/**  * Copyright © 2016 Magento. All rights reserved.  * See COPYING.txt for license details.  */
define([
    'jquery',
    'ko',
    'underscore',
    'uiRegistry',
    'uiLayout',
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function ($, ko, _, registry, layout, dynamicRows) {
    'use strict';

    return dynamicRows.extend({
        defaults: {
            showSpinner: false,
            listens: {
                elems: ''
            },
            currentValue: [],
            currentOption: [],
            scope: "",
        },

        initObservable: function () {
            this._super().observe(true, 'label');

            return this;
        },

        /**
         * Save current option data.
         *
         * @param {Object} params
         */
        setOptionData: function (params) {
            var provider = registry.get(params.provider),
                currentValue = provider.get(params.parentScope),
                currentOption = provider.get(this.getOptionScope(params.parentScope));

            this.scope = params.parentScope;
            this.currentValue = currentValue;
            this.currentOption = currentOption;
        },

        /**
         * Set title for Dynamic Row.
         *
         * @returns {exports}
         */
        setTitle: function () {
            var valueTitle = this.currentValue.title || '',
                optionTitle = this.currentOption.title || '',
                title = (valueTitle == optionTitle) ? optionTitle : optionTitle + ' - ' + valueTitle,
                label = this.defaultLabel + ': ' + title;

            this.set('label', label);

            return this;
        },

        /**
         * Retrieve current option scope based on current option value scope.
         *
         * @param {String} valueScope - current option value scope
         * @returns {String}
         */
        getOptionScope: function (valueScope) {
            return valueScope.split('.values')[0];
        },

        /**
         * Load saved dependencies into dynamic row grid in modal window.
         *
         * @returns {exports}
         */
        loadDependency: function () {
            var designidea_id = registry.get('product_form.product_form.advanced_design_modal.merchandise-personalisation.container_designidea_id.designidea_id');
            console.log('designidea_id.value()',designidea_id.value());
            var records = require('uiRegistry').get('index = designs');
            console.log('records',records);
            records.elems.each(function (record) {
                console.log('record',record);
                record.elems.filter(function (comp) {
                    console.log('designidea_id');
                    if(designidea_id !== undefined && comp.value() == designidea_id.value()){
                        console.log('designidea_id value');
                        jQuery('#'+comp.uid).prop('checked', true).trigger('change')

                    }
                });
            });
            /*var object = this;
            var dependencyContainer = this.currentValue[this.dependencyContainer];
            var savedDependencies = dependencyContainer ? JSON.parse(dependencyContainer) : [];

            savedDependencies = this.checkAvailability(savedDependencies);

            if (!savedDependencies.length) {
                return this;
            }

            for (var i = 0; i < savedDependencies.length; i++) {
                object.addChild(false, i, false); // add row with default data

                var recordScope = object._elems.last(); // get object of currently added row

                // wait when currently added row was added and fill it with saved data
                $.when(registry.promise(recordScope)).then(function(record) {
                    object._fillRow(record, 'option_id', savedDependencies[record.index][0]);
                    object._fillRow(record, 'value_id', savedDependencies[record.index][1]);
                });
            }
*/
            return this;
        },

        /**
         * To fill with data Record.
         *
         * @param {Object} record - object of dynamic row Record
         * @param {String} fieldName - name of field for pasting data to
         * @param {integer} value - option|value id
         * @returns {exports}
         */
        _fillRow: function (record, fieldName, value) {
            if (record) {
                record.getChild(fieldName).value(value);
            }

            return this;
        },

        /**
         * Before load saved dependencies
         * we check each option|value (from saved dependencies array) for existence
         * and modify default saved dependencies array.
         *
         * @param {Array} savedDependencies
         * @returns {Array} Only available options|values
         */
        checkAvailability: function (savedDependencies) {
            var optionIds = this._getAllOptionIds(),
                valueIds = this._getAllOptionValueIds(),
                result = [];

            for (var i = 0; i < savedDependencies.length; i++) {
                var optionId = savedDependencies[i][0],
                    valueId  = savedDependencies[i][1];

                if ($.inArray(optionId, optionIds) !== -1 && $.inArray(valueId, valueIds) !== -1) {
                    result.push(savedDependencies[i]);
                }
            }

            return result;
        },

        _getAllOptionIds: function () {
            var ids = [];
            var provider = registry.get(this.provider),
                data = provider.data,
                options = _.isUndefined(data.mageworx_optiontemplates_group) ? data.product.options : data.mageworx_optiontemplates_group.options;

            for (var i = 0; i < options.length; i++) {
                var option = options[i],
                    isDelete = option['is_delete'],
                    opId = option['mageworx_option_id'] || option['record_id'];

                if (isDelete) {
                    continue;
                }

                ids.push(opId);
            }

            return ids;
        },

        _getAllOptionValueIds: function () {
            var ids = [];
            var provider = registry.get(this.provider),
                data = provider.data,
                options = _.isUndefined(data.mageworx_optiontemplates_group) ? data.product.options : data.mageworx_optiontemplates_group.options;

            for (var i = 0; i < options.length; i++) {
                var option = options[i],
                    isOptionDelete = option['is_delete'],
                    values = option['values'] || [];

                if (isOptionDelete) {
                    continue;
                }

                for (var j = 0; j < values.length; j++) {
                    var value = values[j],
                        isValueDelete = value['is_delete'],
                        valueId = value['mageworx_option_type_id'] || value['record_id'];

                    if (isValueDelete) {
                        continue;
                    }

                    ids.push(valueId);
                }
            }

            return ids;
        },

        saveDependency: function () {
            var dependency = [];
            var records = this.elems();

            for (var i=0; i<records.length; i++) {
                var record = records[i];

                dependency.push([record.data().option_id, record.data().value_id]);
            }

            dependency = dependency.length ? JSON.stringify(dependency) : "";
            console.log('dependency',dependency);
            /*registry
                .get('dataScope = ' + this.scope + '.' + this.dependencyContainer)
                .value(dependency);*/

            return this;
        }
    });
});