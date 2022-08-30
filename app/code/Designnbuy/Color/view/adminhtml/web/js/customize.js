define([
    'jquery'
], function ($) {
    "use strict";

    return function (config, element) {

        $(element).change(function () {
            debugger;
            if(jQuery(this).val() == "category"){
                jQuery('[name="selected_category"]').show();
            } else {
                jQuery('[name="selected_category"]').hide();
            }
        });
    }
});