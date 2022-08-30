/*
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirm) {
    "use strict";

    $.widget('mage.salesrep', {
        options: {},

        open: false,


        _create: function () {
            this.initSubmitEvent();
        },

        initSubmitEvent: function () {
            var self = this;

            $('#form-sr-edit').submit(function (e) {
                if (self.open === false) {
                    e.preventDefault();
                    confirm({
                        title: '',
                        content: 'Are you sure you want to change your manager(s)?',
                        actions: {
                            confirm: function () {
                                self.open = true;
                                $('#form-sr-edit').submit();
                            },
                            cancel: function () {
                            },
                            always: function () {
                            }
                        }
                    });
                }

            });
        }
    });

    return $.mage.salesrep;
});
