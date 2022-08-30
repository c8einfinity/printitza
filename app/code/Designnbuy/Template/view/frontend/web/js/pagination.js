define([
    'jquery',
    'ko'
], function ($,ko) {
    "use strict";

    return function (config, element) {
        var page = 2;
        var pagelimit = config.pagelimit;
        var totalitem = config.pagelimit;
        var product = config.product;

        $(element).click(function () {
            debugger;
            var size = config.size;
            var baseurl = config.baseurl;
            var width = config.width;
            var height = config.height;
            var search = config.search;
            var category_id = config.category_id;
            if(category_id == "" && width == "" && height == "" && search == ""){
                category_id = "all";
            }
            $.ajax({
                url: baseurl+"canvas/product/browsetemplates/",
                type: 'POST',
                dataType: 'json',
                showLoader:true,
                data: {
                    product : product,
                    p : page,
                    width : width,
                    height : height,
                    category_id : category_id,
                    search : search,
                    ajax : true
                },
                complete: function(response) {
                    if(response.responseJSON.category_templates){
                        var div = document.createElement("div");
                        document.body.appendChild(div);
                        div.innerHTML = response.responseJSON.category_templates;
                        var lis = div.getElementsByTagName("li");

                        var relatedProd = jQuery(".products.list.items.product-items");
                        if(relatedProd.length){
                            relatedProd.append(lis).trigger('contentUpdated');
                            page = page + 1;
                            totalitem = parseInt(totalitem) + parseInt(pagelimit);
                            if(totalitem > size){
                                jQuery(".actions-primary.see-more .tocart.template").attr("disabled","disabled");
                            }
                        }
                        document.body.removeChild(div);
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