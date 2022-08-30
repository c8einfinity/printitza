define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox',
    'priceOptions',
], function ($, _, mageTemplate, utils) {
    'use strict';
    var globalOptions = {
        qtyFieldSelector: 'input.qty',
    };
    $.widget('mage.fixedprices', $.mage.priceOptions, {
        options: globalOptions,
        /**
         * @private
         */
        _create: function() {
            console.log('hey, fixedprices is loaded!')
            //bind click event of elem id
            this.element.on('change', function(e){
                console.log('change ME!');
            });
            this.element.on('change', this._onQtyFieldChanged.bind(this));
        },

        _onQtyFieldChanged: function onQtyFieldChanged(event) {
            console.log("Magentoins fixedPrices", this.options);
        }
    });

    return $.mage.fixedprices;

});