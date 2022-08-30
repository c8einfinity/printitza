define([
    'jquery',
    'Magento_Ui/js/form/element/select',
    'mage/storage'
], function ($, Select, storage) {
    'use strict';

    return Select.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input'
        },
        /**
         * Change currently selected option
         *
         * @param {String} id
         */
        selectOption: function(id){
            var url = window.templateimageurl;
            var data = [];
            data.push({"name": "form_key", "value": FORM_KEY});
            
            jQuery(".admin__field.template_image").remove();

            setTimeout(function(){
                var template_id = $("#"+id).val();
                data.push({"name": "id", "value": template_id});    
                $.ajax({
                    showLoader: true,
                    url: url,
                    data: data,
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    if(jQuery(".admin__field.template_image").length == 0){
                        jQuery('[data-index="container_template_id"]').after(data.templateImage);
                    }
                    ;
                });


            }, 500);
        },
    });
});