define([
    'jquery',
    'uiComponent',
    'ko'
],function ($, Component, ko) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Designnbuy_Fixedprices/fixedprice/content'
            },
            initialize: function () {
                this._super();
                var self = this;
                var priceid= ko.observable();
                self.selectedChoice = ko.observable();
                self.selectedPrice=ko.observable();
                this.qty = ko.observable(this.defaultQty);

            },
            changePrice: function () {
                this.increaseQty();
            },
            increaseQty: function() {
                var newQty = parseInt(this.qty()) + 1;
                if (newQty > 100) {
                    newQty = 100;
                }
                this.qty(newQty);
                $('input[name="qty"]').val(newQty);
            },
        });
    }
);