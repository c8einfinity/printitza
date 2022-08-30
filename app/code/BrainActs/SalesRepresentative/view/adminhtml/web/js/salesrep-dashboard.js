/*
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function ($, confirm) {
    "use strict";

    $.widget('mage.salesrepDashboard', {
        options: {},

        _create: function () {
            this.initSubmitEvent();
            this.changeDiagramsPeriod();


            //google.charts.load('current', {'packages': ['corechart', 'line']});
            //google.charts.setOnLoadCallback(drawChart);
        },

        initSubmitEvent: function () {
            var self = this;

            $('#request-withdrawal').submit(function (e) {


                var data = $('#request-withdrawal').serializeArray();
                data.push({"name": "form_key", "value": FORM_KEY});

                e.preventDefault();
                confirm({
                    title: '',
                    content: 'Are you sure you want to request withdrawal?',
                    actions: {
                        confirm: function () {
                            $.ajax({
                                showLoader: true,
                                url: $('#request-withdrawal').attr('action'),
                                data: data,
                                type: "POST",
                                dataType: 'json'
                            }).done(function (data) {
                                console.log(data);
                                window.location.href = data.redirect;
                            });
                        },
                        cancel: function () {
                        },
                        always: function () {


                        }
                    }
                });
            });
        },

        changeDiagramsPeriod: function () {
            $('#_period').change(function (e) {
                e.preventDefault();
                var period = $(this).val();

                var data = [];

                data.push({"name": "period", "value": period});
                data.push({"name": "form_key", "value": FORM_KEY});


                $.ajax({
                    showLoader: true,
                    url: $('#_period').data('url'),
                    data: data,
                    type: "POST",
                    dataType: 'json'
                }).done(function (reportData) {

                    dataV = new google.visualization.DataTable();
                    dataV.addColumn('date', 'Period');
                    dataV.addColumn('number', 'Amount, $');
                    dataV.addRows(reportData.length+1);


                    jQuery.each(reportData, function(index, item){
                        dataV.setCell(index, 0, new Date(item[0]));
                        dataV.setCell(index, 1, item[1]);
                    });

                    //chart = new google.charts.Line(document.getElementById('chart'));
                    chart.clearChart();
                    chart.draw(dataV, google.charts.Line.convertOptions(optionsV));
                });

            });
        }
    });

    return $.mage.salesrepDashboard;
});
