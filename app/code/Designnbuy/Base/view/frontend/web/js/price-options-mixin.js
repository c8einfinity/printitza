define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'underscore',
    'mage/template',
    'jquery/ui',
    'Magento_Catalog/js/price-options'
], function ($, utils, _, mageTemplate) {

    return function (widget) {

        $.widget('mage.priceOptions', widget, {
            _create: function createPriceOptions() {
                this._super();
            },
            _onOptionChanged: function () {
                console.log("This was called instead of the parent _onOptionChanged function");
                console.log(this.options.optionConfig);

                var changes,
                    option = $(this.target),
                    handler = this.options.optionHandlers[option.data('role')];

                option.data('optionContainer', option.closest(this.options.controlContainer));

                if (handler && handler instanceof Function) {
                    changes = handler(option, this.options.optionConfig, this);
                } else {
                    changes = defaultGetOptionValue(option, this.options.optionConfig);
                }
                $(this.options.priceHolderSelector).trigger('updatePrice', changes);
            },


            updatePrice: function updatePrice(newPrices) {
                console.log("This was called instead of the parent updatePrice function");
                var prices = this.cache.displayPrices,
                    additionalPrice = {},
                    pricesCode = [],
                    priceValue, origin, finalPrice;

                this.cache.additionalPriceObject = this.cache.additionalPriceObject || {};

                if (newPrices) {
                    $.extend(this.cache.additionalPriceObject, newPrices);
                }

                if (!_.isEmpty(additionalPrice)) {
                    pricesCode = _.keys(additionalPrice);
                } else if (!_.isEmpty(prices)) {
                    pricesCode = _.keys(prices);
                }

                _.each(this.cache.additionalPriceObject, function (additional) {
                    if (additional && !_.isEmpty(additional)) {
                        pricesCode = _.keys(additional);
                    }
                    _.each(pricesCode, function (priceCode) {
                        priceValue = additional[priceCode] || {};
                        priceValue.amount = +priceValue.amount || 0;
                        priceValue.adjustments = priceValue.adjustments || {};

                        additionalPrice[priceCode] = additionalPrice[priceCode] || {
                                'amount': 0,
                                'adjustments': {}
                            };
                        additionalPrice[priceCode].amount =  0 + (additionalPrice[priceCode].amount || 0) +
                            priceValue.amount;
                        _.each(priceValue.adjustments, function (adValue, adCode) {
                            additionalPrice[priceCode].adjustments[adCode] = 0 +
                                (additionalPrice[priceCode].adjustments[adCode] || 0) + adValue;
                        });
                    });
                });

                if (_.isEmpty(additionalPrice)) {
                    this.cache.displayPrices = utils.deepClone(this.options.prices);
                } else {
                    _.each(additionalPrice, function (option, priceCode) {
                        origin = this.options.prices[priceCode] || {};
                        finalPrice = prices[priceCode] || {};
                        option.amount = option.amount || 0;
                        origin.amount = origin.amount || 0;
                        origin.adjustments = origin.adjustments || {};
                        finalPrice.adjustments = finalPrice.adjustments || {};

                        finalPrice.amount = 0 + origin.amount + option.amount;
                        _.each(option.adjustments, function (pa, paCode) {
                            finalPrice.adjustments[paCode] = 0 + (origin.adjustments[paCode] || 0) + pa;
                        });
                    }, this);
                }

                this.element.trigger('reloadPrice');
            }

        });
        return $.mage.priceOptions;
    }


});