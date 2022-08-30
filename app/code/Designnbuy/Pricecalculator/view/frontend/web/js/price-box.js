define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'underscore',
    'mage/template',
    'jquery/ui'
], function ($, utils, _, mageTemplate) {
    'use strict';

    return function (priceBox) {
        return $.widget('mage.priceBox', priceBox, {
            /*reloadPrice: function reDrawPrices() {
                var priceFormat = (this.options.priceConfig && this.options.priceConfig.priceFormat) || {},
                    priceTemplate = mageTemplate(this.options.priceTemplate);

                _.each(this.cache.displayPrices, function (price, priceCode) {
                    price.final = _.reduce(price.adjustments, function(memo, amount) {
                        return memo + amount;
                    }, price.amount);
                    console.log('ajay',window.squarePrice);


                    //console.log('window.squarePrice',window.squarePrice);
                    if(window.squarePrice){
                        price.final = window.squarePrice + price.final;
                    }
                    price.formatted = utils.formatPrice(price.final, priceFormat);
                    console.log('price',price);
                    console.log('data-price-type',$('[data-price-type="' + priceCode + '"]', this.element));
                    console.log('priceTemplate',priceTemplate({data: price}));
                    //$('div.price-box.price-final_price').html(priceTemplate({data: price}));
                    //$('[data-price-type="' + priceCode + '"]', this.element).html(priceTemplate({data: price}));
                }, this);*/
            }
        });
    };
});