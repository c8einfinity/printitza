define([
    'jquery',
    'ko',
    'uiComponent'
], function ($, ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Drc_Storepickup/delivery-date-block'
        },
        initialize: function () {
            this._super();
            var disabled = window.checkoutConfig.shipping.delivery_date.disabled;
            var noday = window.checkoutConfig.shipping.delivery_date.noday;
            var hourMin = parseInt(window.checkoutConfig.shipping.delivery_date.hourMin);
            var hourMax = parseInt(window.checkoutConfig.shipping.delivery_date.hourMax);
            var format = window.checkoutConfig.shipping.delivery_date.format;
            var holidaysData = window.checkoutConfig.holidaysData;
            
            if (!format) {
                format = 'yy-mm-dd';
            }
            var disabledDay = disabled.split(",").map(function (item) {
                return parseInt(item, 10);
            });

            ko.bindingHandlers.datetimepicker = {
                init: function (element, valueAccessor, allBindingsAccessor) {
                    var $el = $(element);
                    //initialize datetimepicker
                    if (noday) {
                        var options = {
                            minDate: 0,
                            dateFormat:format,
                            hourMin: hourMin,
                            hourMax: hourMax,
                            beforeShowDay: function (date) {
                                var day = date.getDay();
                                var today = new Date();
                               /*  today.setDate(11);
                                today.setMonth(2); */
                                var day3 = new Date(today.getTime() + (24 * min_prod_time * 60 * 60 * 1000));

                                /*Disable Date based on Holidays @ap*/
                                var holidaysDisabledDates = window.checkoutConfig.holidaysData;
                                var holidaysDateDisabledEachYear = window.checkoutConfig.holidaysEachYearData;
                                var string = jQuery.datepicker.formatDate('dd-mm-yy', date);
                                if(holidaysDisabledDates.indexOf(string) > -1) {
                                    if(date > today && date <= day3) min_prod_time++;                                    
                                    return [false];
                                }
                                /*End*/ 
                                
                                var string = jQuery.datepicker.formatDate('dd-mm', date);
                                if(holidaysDateDisabledEachYear.indexOf(string) > -1) {                                    
                                    if(date > today && date <= day3) min_prod_time++;
                                    return [false];
                                }
                                
                                if (disabledDay.indexOf(day) > -1) {
                                    if(date > today && date <= day3) min_prod_time++;
                                    return [false];
                                } else {
                                    if(date < day3){
                                        return [false];
                                    }
                                    return [true];
                                }
                            }
                        };
                    } else {
                        
                        var options = {
                            minDate: 0,
                            dateFormat:format,
                            hourMin: hourMin,
                            hourMax: hourMax,
                            beforeShow: function (date) {
                                
                                setTimeout(function(){full_and_final = min_prod_time;},500);
                            },
                            beforeShowDay: function (date) {
                                var today = new Date();
                               /*  today.setDate(11);
                                today.setMonth(2); */
                                var day3 = new Date(today.getTime() + (24 * min_prod_time * 60 * 60 * 1000));
                                
                                /*Disable Date based on Holidays @ap*/
                                var holidaysDateBetween = window.checkoutConfig.holidaysData;
                                var holidaysDateDisabledEachYear = window.checkoutConfig.holidaysEachYearData;
                                var holidaysDisabledDates = holidaysDateBetween;
                                var string = jQuery.datepicker.formatDate('dd-mm-yy', date);
                                if(holidaysDisabledDates.indexOf(string) > -1) {       
                                    if(date > today && date <= day3) min_prod_time++;
                                    return [false];
                                }
                                /*End*/ 
                                
                                var string = jQuery.datepicker.formatDate('dd-mm', date);
                                if(holidaysDateDisabledEachYear.indexOf(string) > -1) {     
                                    if(date > today && date <= day3) min_prod_time++;
                                    return [false];
                                }
                                
                                var day = date.getDay();
                                if (disabledDay.indexOf(day) > -1) {
                                    if(date > today && date <= day3) min_prod_time++;
                                    return [false];
                                } else {
                                    if(date < day3){
                                        return [false];
                                    }
                                    return [true];
                                }

                            },
                            onClose: function () {
                                min_prod_time = window.checkoutConfig.productionTime;
                           },
                           onSelect: function(selectedDate) {
                                min_prod_time = full_and_final;
                           },
                           onChangeMonthYear: function () {
                                min_prod_time = window.checkoutConfig.productionTime;
                            }
                           
                        };
                    }
                    
                    var min_prod_time = window.checkoutConfig.productionTime;
                    //var min_prod_time = 3;
                    $el.datetimepicker(options);
                    var full_and_final = min_prod_time;

                    var writable = valueAccessor();
                    if (!ko.isObservable(writable)) {
                        var propWriters = allBindingsAccessor()._ko_property_writers;
                        if (propWriters && propWriters.datetimepicker) {
                            writable = propWriters.datetimepicker;
                        } else {
                            return;
                        }
                    }
                    writable($(element).datetimepicker("getDate"));
                },
                update: function (element, valueAccessor) {
                    var widget = $(element).data("DateTimePicker");
                    //when the view model is updated, update the widget
                    if (widget) {
                        var date = ko.utils.unwrapObservable(valueAccessor());
                        widget.date(date);
                    }
                }
            };

            return this;
        }
    });
});
