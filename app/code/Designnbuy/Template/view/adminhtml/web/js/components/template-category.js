/**
 * Copyright © 2018 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/element/ui-select',
    'uiRegistry',
    'underscore',
    'ko',
    'mage/translate'
], function ($, Select, registry, _, ko, $t) {
    'use strict';

    return Select.extend({

        defaults: {
            showPath: false,
            isEnabledTitleId: false,
            startPoint: '',
            endPoint: '',
            previousEndPointOption: '',
            previousEndPointOptionValue: '',
            needToUsePrevEndPoint: false,
            massSelectedOptions: [],
            target: {
                template_category: '${ $.parentName }.template_category',
                template_id: '${ $.parentName }.template_id',
            }
        },
        initialize: function () {
            this._super();

            if (this.filterBy) {
                console.log('filterBy');
                this.initFilter();
            }

            return this;
        },

        /**
         * Set link for filter.
         *
         * @returns {Object} Chainable
         */
        initFilter: function () {
            console.log('initFilter');
            var filter = this.filterBy;

            this.filter(this.default, filter.field);
            this.setLinks({
                filter: filter.target
            }, 'imports');

            return this;
        },

        /**
         * Load all available options.
         */
       /* loadOptionsList: function (currentCateogryId) {
            var  provider = registry.get(this.provider),
                data = provider.data;
            var categoryTemplates = data.product.templates;
            this.loadValuesList(data.product.template_category);
        },*/

        /**
        * Filters 'initialOptions' property by 'field' and 'value' passed,
        * calls 'setOptions' passing the result to it
        *
        * @param {*} value
        * @param {String} field
        */
        filter: function (value, field) {
            var source = this.initialOptions,
                result;

            field = field || this.filterBy.field;
            console.log('field',field);
            result = _.filter(source, function (item) {
                return item[field] === value || item.value === '';
            });

            this.setOptions(result);
        },


    });
});
