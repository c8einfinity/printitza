define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal',
    'jquery'
], function (_, uiRegistry, select, modal,jQuery) {
    'use strict';
    return select.extend({      

        hasAddons: function (){
            debugger;
            //var value = this._super().initialValue;    
            setTimeout(function(){
                var element_color_picker_type = uiRegistry.get('index = element_color_picker_type');
                var element_color_settings = jQuery('select[name ="product[element_color_settings]"]');

                console.log("type "+element_color_picker_type.length);
                console.log("setting "+element_color_settings.length);
                

                if(element_color_picker_type !== undefined && element_color_settings.length){

                    if (element_color_settings.val() == 1) {
                        
                        jQuery('select[name ="product[element_color_picker_type]"] option[value ="1"]').show();
                        jQuery('select[name ="product[element_color_picker_type]"] option[value ="3"]').hide();
    
                    } else if (element_color_settings.val() == 2) {
    
                        jQuery('select[name ="product[element_color_picker_type]"] option[value ="3"]').show();
                        jQuery('select[name ="product[element_color_picker_type]"] option[value ="1"]').hide();
                    }
                    //element_color_picker_type.initialize();     
                }
                console.log("setting "+element_color_settings.val());
                
            }, 1000);
            
            
            return this._super();

        },
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            
            var element_color_picker_type = uiRegistry.get('index = element_color_picker_type');
            
            if(element_color_picker_type !== undefined){
                if (value == 1) {
                    
                    jQuery('select[name ="product[element_color_picker_type]"] option[value ="1"]').show();
                    jQuery('select[name ="product[element_color_picker_type]"] option[value ="3"]').hide();

                } else if (value == 2) {

                    jQuery('select[name ="product[element_color_picker_type]"] option[value ="3"]').show();
                    jQuery('select[name ="product[element_color_picker_type]"] option[value ="1"]').hide();
                }
                element_color_picker_type.initialize();     
            }
            
            
            return this._super();
        },
    });
});