define(['jquery'], function ($) {

    return function (widget) {

        $.widget('mage.priceOptions', widget, {

            _onOptionChanged: function () {
                //console.log('this.options.optionConfig',this.options.optionConfig);
                //$(this.options.priceHolderSelector).trigger('updatePrice', changes);
            }

        });
        return $.mage.priceOptions;
    }
});