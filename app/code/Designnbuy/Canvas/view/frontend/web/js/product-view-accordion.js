require([
'jquery',
'jquery/ui',
'mage/collapsible'
], function($) {
    jQuery('.accordion-title-price.collapsibleTab').attr('data-role','title');
    jQuery('.product-accordion-box.collapsibleContent').attr('data-role','content');
    

    if(jQuery(".product-accordion-list .accordion-block.quickedit-accordion").length){
    	jQuery(".product-accordion-list .accordion-block:not(.quickedit-accordion)").collapsible({ "openedState": "active", "collapsible": true, "multipleCollapsible": true, icons: {"header": "icon-angle-down", "activeHeader": "icon-angle-up"}, "animate":{"duration":"400"}});
    	jQuery(".product-accordion-list .accordion-block.quickedit-accordion").collapsible({ "active": [1,0], "openedState": "active", "collapsible": true, "multipleCollapsible": true, icons: {"header": "icon-angle-down", "activeHeader": "icon-angle-up"}, "animate":{"duration":"400"}});	
    } else {
    	jQuery(".product-accordion-list .accordion-block:not(.product-custom-options)").collapsible({ "openedState": "active", "collapsible": true, "multipleCollapsible": true, icons: {"header": "icon-angle-down", "activeHeader": "icon-angle-up"}, "animate":{"duration":"400"}});
    	jQuery(".product-accordion-list .accordion-block.product-custom-options").collapsible({ "active": [1,0], "openedState": "active", "collapsible": true, "multipleCollapsible": true, icons: {"header": "icon-angle-down", "activeHeader": "icon-angle-up"}, "animate":{"duration":"400"}});    	
    }
    
    
    jQuery(document).ready(function(){
        var cnt_up = jQuery("#product-upload-button,#customize,#product-browse-templates-button").length;
        jQuery("#product-upload-button,#customize,#product-browse-templates-button").attr("style", `width:calc((100% / ${cnt_up}) - 5px) !important`);
        var cnt_down = jQuery("#product-enquiry-button,#product-addtocart-button,#product-addtoquote-button").length;
        jQuery("#product-enquiry-button,#product-addtocart-button,#product-addtoquote-button").attr("style", `width:calc((100% / ${cnt_down}) - 5px) !important`);

        jQuery(".reviews-actions a.action.add").on("click",function(){
            jQuery("#tab-label-reviews-title").click();
        });
    });
});