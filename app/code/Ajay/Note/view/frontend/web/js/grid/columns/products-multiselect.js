/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/grid/columns/multiselect',
    'uiRegistry'
], function (jQuery, MultiSelect, uiRegistry) {
    'use strict';

    return MultiSelect.extend({
        defaults: {
            label: '',
            headerTmpl: 'ui/grid/columns/text',
            bodyTmpl: 'Ajay_Note/grid/cells/radio',
        },
        /**
         * Initializes observable properties.
         *
         * @returns {Multiselect} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
                    'disabled',
                    'selected',
                    'excluded',
                    'excludeMode',
                    'totalSelected',
                    'allSelected',
                    'indetermine',
                    'totalRecords',
                    'rows'
                ]);

            return this;
        },
        /**
         * Initializes column component.
         *
         * @returns {Column} Chainable.
         */
        initialize: function () {
            this._super()
                .initFieldClass();

            //var form = uiRegistry.get('mageworx_optiontemplates_group_form.mageworx_optiontemplates_group_form');
            //var products = form.source.data.mageworx_optiontemplates_group.products;
            //var source = uiRegistry.get('ajay_note_note_index.ajay_note_note_grid_data_source');
            //var products = source.data.products;
            var products = window.products;

            //var products = ["1","2","3"];
            var parent = this;
            jQuery(products).each(function (i, t) {
                parent.select(t, false);
            });

            return this;
        },

        /**
         * Selects specified record.
         *
         * @param {*} id - See definition of 'getId' method.
         * @param {Boolean} [isIndex=false] - See definition of 'getId' method.
         * @returns {Multiselect} Chainable.
         */
        select: function (id, isIndex) {
            this._setSelection(id, isIndex, true);
            this.addToForm(id);
            return this;
        },


        /**
         * Deselects specified record.
         *
         * @param {*} id - See definition of 'getId' method.
         * @param {Boolean} [isIndex=false] - See definition of 'getId' method.
         * @returns {Multiselect} Chainable.
         */
        deselect: function (id, isIndex) {
            this._setSelection(id, isIndex, false);
            this.removeFromForm(id);
            return this;
        },

        /**
         * Toggles selection of a specified record.
         *
         * @param {*} id - See definition of 'getId' method.
         * @param {Boolean} [isIndex=false] - See definition of 'getId' method.
         * @returns {Multiselect} Chainable.
         */
        toggleSelect: function (id, isIndex) {
            var isSelected = this.isSelected(id, isIndex);
            this._setSelection(id, isIndex, !isSelected);
            if (isSelected) {
                this.removeFromForm(id);
            } else {
                this.addToForm(id);
            }
            this._super();
            return this;
        },

        addToForm: function (id) {
            var products = this.getProductsFromFromSource();
            var position = jQuery.inArray(id, products);
            if (position == -1) {
                products.push(id);
            }
        },

        removeFromForm: function (id) {
            var products = this.getProductsFromFromSource();
            var position = jQuery.inArray(id, products);
            if (position != -1) {
                delete products[position];
            }
        },

        getProductsFromFromSource: function () {
            /*var form = uiRegistry.get('mageworx_optiontemplates_group_form.mageworx_optiontemplates_group_form');
            try {
                var products = form.source.data.mageworx_optiontemplates_group.products;
            } catch (e) {
                products = [];
            }*/
            var source = uiRegistry.get('ajay_note_note_index.ajay_note_note_grid_data_source');
            var products = source.data.products;
            try {
                var products = source.data.products;
            } catch (e) {
                products = [];
            }

            var products = ["1","2","3"];
            return products;
        },

        /**
         * Callback method to handle changes of selected items.
         *
         * @param {Array} selected - An array of currently selected items.
         */
        onSelectedChange: function (selected) {
            /*var provider = uiRegistry.get('ajay_note_note_index.ajay_note_note_index.ajay_note_note_columns.ids');

            if(provider){
                provider.deselectAll();
                provider.selections().deselectAll();
            }*/
            this.updateExcluded(selected)
                .countSelected()
                .updateState();
        },
    });
});
