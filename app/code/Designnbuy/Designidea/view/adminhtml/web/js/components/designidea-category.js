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
                designidea_category: '${ $.parentName }.designidea_category',
               // designidea_id: '${ $.parentName }.designidea_id',
            }
        },
        initialize: function () {
            this._super();
            this.loadOptionsList(registry.get(this.target.designidea_category));
            return this;
        },
        /**
         * Load all available options.
         */
        loadOptionsList: function (currentCateogryId) {
            var  provider = registry.get(this.provider),
                data = provider.data;
            console.log('data',data.product);
            var categoryDesignIdeas = data.product.designideas;
            this.loadValuesList(data.product.designidea_category);
        },
        toggleOptionSelected: function (data) {
            this._super();
            var isSelected = this.isSelected(data.value);
            if(isSelected){
                this.loadValuesList(data.value);
            }
        },

        /**
         * Load all available values for selected option.
         *
         * @param {integer} currentOptionId
         */
        loadValuesList: function (currentCateogryId) {
            var list = [],
                provider = registry.get(this.provider),
                data = provider.data;
            var categoryDesignIdeas = data.product.designideas;
            var designideas = [];
            var selectedOptionValues = [];
            $.each(categoryDesignIdeas, function(index, item){

                if(currentCateogryId == index){
                    selectedOptionValues = item;
                    return false;     // breaks the $.each() loop
                }
            });
            selectedOptionValues = this.getSelectedCatgegoryValues(selectedOptionValues);

            var designidea_id = registry.get('product_form.product_form.advanced_design_modal.merchandise-personalisation.container_designidea_id.designidea_id');
            var designIdeaInitialValue = designidea_id.initialValue;
            console.log('designidea_id',designidea_id);
            if(designidea_id){
                designidea_id.cacheOptions = {};
                designidea_id.cacheOptions.tree = selectedOptionValues;
                designidea_id.cacheOptions.plain = selectedOptionValues;
                designidea_id.reset();
                designidea_id.clear();
                designidea_id.options([]);
                //designidea_id.value = designIdeaInitialValue;
                //designidea_id.options(selectedOptionValues);
            }
        },

        getSelectedCatgegoryValues: function (selectedOption) {
            var selectedOptionValues = selectedOption,
                result = [];

            selectedOptionValues.forEach(function (option) {
                result.push({
                    'label': option.title,
                    'value': option.id,
                    'record_id': option.id,
                    'initialize': option.id,
                });

            });

            return result;
        },

    });
});
