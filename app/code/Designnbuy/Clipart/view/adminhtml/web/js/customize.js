/**
 * Created by Ajay on 27-09-2017.
 */
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