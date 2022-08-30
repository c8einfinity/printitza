/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
var threedConfigArea;
define([
    'Magento_Ui/js/form/components/html',
    'uiRegistry',
    'underscore',
    'jquery',
    'Magento_Ui/js/modal/alert',
    "mage/backend/tabs",
    "fabric",
], function (Html, registry, _, $, alert) {
    'use strict';
    threedConfigArea = this;
    return Html.extend({
        defaults: {
            configurableArea: [],
            sides: [],
            rectArray: [],
            canvasArray: [],
            prodImageArray: [],
            currentSide: '',
            canvas: null,
            saveUrl: '',
        },
        getThreedData: function () {

        },
        setThreedData: function () {
            console.log("this.canvas");
            console.log(this.canvas);
        },
        /**
         * Updates component visibility state.
         *
         * @param {Boolean} variationsEmpty
         * @returns {Boolean}
         */
        onCanvasRender: function (element) {
            console.log(element);
            var configurableArea = [];
            var updateValues = [];
            var productDataSourceKey = 'product_form.product_form_data_source';
            var entity = registry.get(productDataSourceKey);
            var product = entity.data.product;
            this.configurableAreas = product.threed_configurable_areas;
            if(product.threed_configure_area){
                configurableArea.area = JSON.parse(product.threed_configure_area);
            }
            this.saveUrl = this.configurableAreas.saveUrl;
            if(product.map_image){
                var mapImage = product.map_image.file[0];
                configurableArea.image = mapImage.url;
                //console.log('mapImage',mapImage);
            }
            console.log('product',product);
            var updateValues = false;
            this.createConfigureArea(element, configurableArea, updateValues);
        },
        createConfigureArea: function (canvasId, configurableArea, updateValues) {
            self = this;
            var global_options = configurableArea;
            console.log("configurableArea");
            console.log(configurableArea);
            console.log("this.canvas");
            console.log(this.canvas);
            if(!this.canvas){
                this.canvas = new fabric.Canvas(canvasId, { width: 500, height: 500 });
            }
            console.log("this.canvas");
            console.log(this.canvas);
            var area = {
                pos_x: 150,
                pos_y: 150,
                width: 150,
                height: 150
            }
            if (global_options.area) {
                area = global_options.area;
            }

//change color of container
    $(".canvas-container").css('background','#e3e3e3');
    //
            // "add" rectangle onto canvas

            fabric.Image.fromURL(global_options.image, function (oImg) {
                var prod_image = oImg;
                self.prodImageArray.push(prod_image);

                var imagewidth = oImg.width;
                var imageheight = oImg.height;
                var ratio = imagewidth / imageheight;
                if (imagewidth >= imageheight) {
                    imagewidth = 400;
                    imageheight = imagewidth / ratio;
                    if (imageheight > 485) {
                        imageheight = 485;
                        imagewidth = ratio * imageheight;
                    }
                } else {
                    imageheight = 485;
                    imagewidth = ratio * imageheight;
                    if (imagewidth > 400) {
                        imagewidth = 400;
                        imageheight = imagewidth / ratio;
                    }
                }
                // oImg.width = imagewidth;
                // oImg.height = imageheight;
                oImg.set("scaleX", imagewidth / oImg.width);
                oImg.set("scaleY", imageheight / oImg.height);
                oImg.set("left", (500 - imagewidth) / 2);
                oImg.set("top", (500 - imageheight) / 2);
                // canvas.add(oImg);
                self.canvas.setBackgroundImage(oImg);
                oImg.set('selectable', false); // make object unselectable

                console.log("area");
                console.log(area);
                console.log("left");
                console.log(parseFloat(prod_image.get("left")) + parseFloat(area.pos_x));
                console.log("top");
                console.log(parseFloat(prod_image.get("top")) + parseFloat(area.pos_y));
                rect = new fabric.Rect({
                    left: parseFloat(prod_image.get("left")) + parseFloat(area.pos_x),
                    top: parseFloat(prod_image.get("top")) + parseFloat(area.pos_y),
                    fill: '',
                    stroke: "red",
                    width: parseFloat(area.width) || 150,
                    config_class: self,
                    height: parseFloat(area.height) || 150
                });
                self.canvas.add(rect);
                self.canvas.setActiveObject(rect);
                self.rectArray.push(rect);

                self.canvas.on('object:selected', self.setCoordinates);
                self.canvas.on('object:moving',  self.setCoordinates);
                self.canvas.on('object:scaling',  self.setCoordinates);
                //if (updateValues)
                    self.setCoordinates(null, self);

                var pos_x = $("#threed_configure_area :input[name=pos_x]");
                var pos_y = $("#threed_configure_area :input[name=pos_y]");
                var width = $("#threed_configure_area :input[name=width]");
                var height = $("#threed_configure_area :input[name=height]");
               

                pos_x.on("input", {self:self},  self.setRect);
                pos_y.on("input", {self:self},  self.setRect);
                width.on("input", {self:self},  self.setRect);
                height.on("input", {self:self},  self.setRect);
                
            });

            $('#threed_configure_area button[type=button]').click(function () {
                self.saveDesignArea(this.form);
            });
        },
        setCoordinates : function (event, self) {
            var currentFormId = getActiveConfigAreaForm();

            var pos_x = $("#" + currentFormId + " :input[name=pos_x]");
            var pos_y = $("#" + currentFormId + " :input[name=pos_y]");
            var width = $("#" + currentFormId + " :input[name=width]");
            var height = $("#" + currentFormId + " :input[name=height]");
            var output_width = $("#" + currentFormId + " :input[name=output_width]");
            var output_height = $("#" + currentFormId + " :input[name=output_height]");

            var currentConfigureAreaRect;
            var activeTabId = 0;
            console.log("activeTabId");
            console.log(activeTabId);
            if (event && event.target) {
                console.log("event");
                console.log(event);
                self = event.target.config_class;
                currentConfigureAreaRect = event.target;
            } else {
                // console.log("self.currentSide.area");
                // console.log(self.currentSide.area);
                currentConfigureAreaRect = rect;
            }
            var prod_image = self.prodImageArray[activeTabId];
            console.log("currentConfigureAreaRect");
            console.log(currentConfigureAreaRect);
            pos_x.val(Number(Math.round(currentConfigureAreaRect.left) - parseFloat(prod_image.get("left"))).toFixed(2));
            pos_y.val(Number(Math.round(currentConfigureAreaRect.top) - parseFloat(prod_image.get("top"))).toFixed(2));
            width.val(Number(Math.round(currentConfigureAreaRect.width) * currentConfigureAreaRect.scaleX).toFixed(2));
            height.val(Number(Math.round(currentConfigureAreaRect.height) * currentConfigureAreaRect.scaleY).toFixed(2));
        },
        setRect: function (evt) {
            // alert(evt.target.value);
            var self = evt.data.self;
            var activeTabId = 0;
            var rect = self.rectArray[activeTabId];
            var prod_image = self.prodImageArray[activeTabId];

           
            if(evt.target.name == "pos_x"){
                rect.set({ left: parseFloat(evt.target.value) + parseFloat(prod_image.get("left")) });
            }else if(evt.target.name == "pos_y"){
                rect.set({ top: parseFloat(evt.target.value) + parseFloat(prod_image.get("left")) });
            }else if(evt.target.name == "width"){
                rect.set({ width: parseFloat(evt.target.value) / rect.scaleX });
            }else if(evt.target.name == "height"){
                rect.set({ height: parseFloat(evt.target.value) / rect.scaleY });
            }
            self.canvas.renderAll();
        },
        saveDesignArea: function (form) {
            var data = form.serialize().split("&");
            console.log("data");
            console.log(data);
            var threed_configure_area={};
            for(var key in data)
            {
                if(typeof(data[key]) == "string" )
                    threed_configure_area[data[key].split("=")[0]] = data[key].split("=")[1];
            }
            /*Save Attribute value in product object*/
            var productDataSourceKey = 'product_form.product_form_data_source';
            var entity = registry.get(productDataSourceKey);
            var product = entity.data.product;
            product.threed_configure_area = threed_configure_area;

            $.ajax({
                url: this.saveUrl,
                type: 'POST',
                dataType: 'json',
                data: form.serialize(),
                complete: function (response) {
                    if (response.responseText.isJSON()) {
                        var json = response.responseText.evalJSON();
                        alert({
                            title: '',
                            content: json.message
                        });
                    }

                },
                error: function (response) {
                    if (response.responseText.isJSON()) {
                        var json = response.responseText.evalJSON();
                        alert({
                            title: '',
                            content: json.message
                        });
                    }
                }
            });
        }
        
        
    });
    var rect;
    function getActiveConfigAreaForm(ui) {
        //var activeTab = ui.newTab.attr("aria-controls");
        return 'threed_configure_area';
    }
           
});
