define([
    'jquery',
    'ko'
], function ($,ko) {
    "use strict";

    return function (config, element) {
        var pagelimit = config.pagelimit;
        var totalitem = config.pagelimit;
        var product = config.product;
        
        

        $(document).on('submit','form#toolbar-form',function(){
            
            var page_toolbar = 1;
            var size = config.size;
            var baseurl = config.baseurl;
            
            var width;
            var height;
            var search;
            if(jQuery("#template-size").length){
                
                width = jQuery("#template-size :selected").attr('width');
                height = jQuery("#template-size :selected").attr('height');
            }
            if(jQuery("#template_search").length){
                search = jQuery("#template_search").val();
            }

            $.ajax({
                url: baseurl+"canvas/product/browsetemplates/",
                type: 'POST',
                dataType: 'json',
                showLoader:true,
                data: {
                    product : product,
                    p : page_toolbar,
                    product_params : jQuery("#product_params").val(),
                    width : width,
                    height : height,
                    search : search,
                    ajax : true
                },
                complete: function(response) {
                    if(response.responseJSON && response.responseJSON.category_templates){
                        var relatedProd = jQuery(".canvas-product-browsetemplates .column.main .templates-grid.products-related");
                        
                        if(relatedProd.length){
                            var prevElem = relatedProd.prev();
                            relatedProd.remove();
                            prevElem.after(response.responseJSON.category_templates).trigger('contentUpdated');
                            
                        }
                    }
                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again.');
                }
            });

            return false;
        });
    }
});