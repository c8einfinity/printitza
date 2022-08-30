/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */


define(['jquery'], function ($) {
    /*var mixin = {

     submitForm: function (form) {
     var addToCartButton, self = this;
     alert(111);
     return;
     if (form.has('input[type="file"]').length && form.find('input[type="file"]').val() !== '') {
     self.element.off('submit');
     // disable 'Add to Cart' button
     addToCartButton = $(form).find(this.options.addToCartButtonSelector);
     addToCartButton.prop('disabled', true);
     addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
     form.submit();
     } else {
     self.ajaxSubmit(form);
     }
     }
     };*/
    return function (widget) {

        $.widget('mage.catalogAddToCart', widget, {
            _bindSubmit: function () {
               // alert("This was called instead of the parent submitForm function");
            },
            submitForm: function () {
               // alert("This was called instead of the parent submitForm function");
            }

        });
        return $.mage.catalogAddToCart;
    }
});



