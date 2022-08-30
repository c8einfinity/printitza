require([
    'jquery'
], function ($) {
    'use strict';
    jQuery(document).ready(function(){
        jQuery('body').on("change","#productsGrid_table .col-in_product .checkbox",function() {
            var not_logedin_ids = [];
            
            jQuery("#productsGrid_table .col-in_product .checkbox").each(function(){
                if(!jQuery(this).is(":checked")) {
                    not_logedin_ids.push(jQuery(this).val());
                }
            });
            jQuery('.cust-group-not-loged-in #Select_Products [name="products"]').val(not_logedin_ids.join("&"));
            

        });
    });
});