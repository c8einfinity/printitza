require([
	'jquery',
	'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'underscore',
    'sidebar',
	'Magento_Catalog/product/view/validation'
	
], 
function($){ 
	"use strict";
	
	jQuery.noConflict();
	$(document).ready(function($){

		var please_wait = $('#lumaplaza-estshipping-cost-loading');
		var popup_success = $('#est-shipping-cost');
		please_wait.hide();
		popup_success.hide();
		$('#estshippingcost_content_option_product').hide();
		
		/* click add to cart in general page or parent category */

		$('body').on('click', ".btn-calculate-shipping-wrapper", function(event) {
			event.preventDefault();
			please_wait.show();
			var product_id = $(this).parents().filter("div.product-item-info").find(".price-box").attr("data-product-id");
			var form_key = $("input[name='form_key']").attr("value");
			var url = estimasteUrl+"ajax/1/utype/general-add";
			var data = {
				'form_key'	:	form_key,
				'product' 	: 	product_id
			};
			
			$.ajax({
				url: url,
				data:data,
				type: 'post',
				dataType: 'json',
				success: function(res) {	
					please_wait.hide();
					if(res["error"]==0){			
						popup_success.html(res["html_popup"]);
						popup_success.show();
						$('#estshipping-cost-shadow').show();

						if(res['item']){
							$('#item_'+res["item"]).remove();
						}   

						return false;
					}else if(res["popup_option"]==1){
						$('#shipping-result-form').addClass("shipping-result-form-border");
						$('#estshippingcost_content_option_product .est_option_content').html(res["html_popup_option"]);
						$('#estshippingcost_content_option_product').show();
						$('#estshipping-cost-shadow').show();								
					}else{
						$('#shipping-result-form').removeClass("shipping-result-form-border");
						$('#estshipping-cost-shadow').hide();
					}
				}
			});
		
		});
		
		
		/*add cart in product detail page*/
		var please_wait = $('#lumaplaza-estshipping-cost-loading');
		var popup_success = $('#est-shipping-cost');
		popup_success.hide();

		$(document).on('click','#calculate-shipping',function(e) {
			
			var cartForm = $('#product_addtocart_form');
			
			if($(".li_estimate_country_id .select2-selection__placeholder").length) {
				alert("Please choose Country");
				$(".li_estimate_country_id .select2").addClass("validate-my-input");
				return false;
			} else {
				$(".li_estimate_country_id .select2").removeClass("validate-my-input");
			}
			
			if($("#input_region_id").css('display') == 'block' && $("#input_region_id").val() == "") {
				alert("Please choose State/Province");
				$("#input_region_id").focus();
				$("#input_region_id").addClass("validate-my-input");
				return false;
			} else {
				$("#input_region_id").removeClass("validate-my-input");
			}
			
			if($("#estimate_region_id").css('display') == 'block' && $(".li_estimate_region_id .select2-selection__placeholder").length) {
				alert("Please choose State/Province");
				$("#input_region_id").focus();
				$(".li_estimate_region_id .select2").addClass("validate-my-input");
				return false;
			} else {
				$(".li_estimate_region_id .select2").removeClass("validate-my-input");
			}
			
			if($("#estimate_postcode").val() == "") {
				alert("Please choose Zip/Postal Code");
				$("#estimate_postcode").focus();
				$("#estimate_postcode").addClass("validate-my-input");
				return false;
			} else {
				$("#estimate_postcode").removeClass("validate-my-input");
			}
			
			var isValid = cartForm.validation('isValid');
			if(isValid) {
				please_wait.show();
				$('#estshipping-cost-shadow').show();
				e.preventDefault();
				e.stopImmediatePropagation();

				$.ajax({
					url: estimasteUrl+"ajax/1/utype/detail-add",
					data: cartForm.serialize(),
					type: 'post',
					dataType: 'json',
					beforeSend: function() {
						
					},
					success: function(res) {
						if(res["error"]==0){
							$('#shipping-result-form').addClass("shipping-result-form-border");
							$('#shipping-result-form').html(res["shipping_estimaste"]);
							please_wait.hide();
							$('#estshipping-cost-shadow').hide();
						}else{
							$('#shipping-result-form').removeClass("shipping-result-form-border");
							$('#estshipping-cost-shadow').hide();
						}
						$('html,body').animate({scrollTop: $("#shipping-result-form").offset().top}, 'slow');
					},
					error: function (response) {
						please_wait.hide();                      
					}
				});
				e.preventDefault();
				return false;
			} else {
				alert("[NOTICE] Please select default product options");
			}
		});
	});
});