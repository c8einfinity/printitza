define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
    'use strict';
    return select.extend({      

        initialize: function (){

            var value = this._super().initialValue;    

            var color_category = uiRegistry.get('index = color_category');
            var single_color_hex_code = uiRegistry.get('index = single_color_hex_code');

                if(color_category !== undefined){
                    if (value == 2) {
                        color_category.show();
                    } else {
                        color_category.hide();
                    }
                }
                if(single_color_hex_code !== undefined){
                    if(value == 3){
                        single_color_hex_code.show();
                    }else {
                        single_color_hex_code.hide();
                    }
                }
            
            return this;

        },
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            
            var color_category = uiRegistry.get('index = color_category');
            var single_color_hex_code = uiRegistry.get('index = single_color_hex_code');

            if(color_category !== undefined){
                if (value == 2) {
                    color_category.show();
                } else {
                    color_category.hide();
                }           
            }
            if(single_color_hex_code !== undefined){
                if(value == 3){
                    single_color_hex_code.show();
                }else {
                    single_color_hex_code.hide();
                }
            }
            return this._super();
        },
    });
});