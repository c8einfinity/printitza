define([
    'Magento_Ui/js/modal/modal-component',
    'uiRegistry',
    'underscore',
    'jquery',
    'Magento_Ui/js/modal/alert',
    "mage/backend/tabs",
    "fabric",
    "Designnbuy_ConfigArea/js/config_area",
], function (Modal, registry, _, $, alert, configArea) {
    'use strict';

    return Modal.extend({
        defaults: {
            stepWizard: '',
            modules: {
                form: '${ $.formName }'
            },
            selectedConfigureAreaIds: [],
            configurableAreas: [],
            sides: [],
            valid: true,
            rectArray: [],
            canvasArray: [],
            prodImageArray: [],
            currentSide: '',
            saveUrl: '',
        },
        initialize: function () {
            var self = this;
            this._super();
        },
        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super().observe(true);
            $("#grid_tab").tabs();
            return this;
        },
        /**
         * Open modal
         */
        openModal: function () {
            this._super();
        },

        actionDone: function () {
            this._super();
        },
        /**
         * Close modal
         */
        closeModal: function () {
            this._super();
            $('div#grid_tab ul li').remove();
            $('div#grid_tab div').remove();
            $("div#grid_tab").tabs("refresh");
        },

        setConfigIds: function (params) {
            console.log('setConfigIds');
            var provider = registry.get(params.provider),
                currentSide = provider.get(params.parentScope);

            var configurableAreas = this.configurableAreas = provider.data.product.configurable_areas;

            this.sides = provider.data.product.sides_configuration;

            this.saveUrl = this.configurableAreas.saveUrl;
            this.selectedConfigureAreaIds = currentSide.configure_areas;


            $.each(this.selectedConfigureAreaIds, function (i, selectedConfigureAreaId) {
                $.each(configurableAreas, function (i, configurableArea) {
                    if (configurableArea.id == selectedConfigureAreaId) {
                        if(currentSide.area.length == 0){
                            currentSide.area[selectedConfigureAreaId] = configurableArea.area;
                        }
                    }
                });
            });
            this.currentSide = currentSide;
            this.configurableAreas = this.updateSides(this.configurableAreas, this.sides, this.currentSide.value_id);
            this.createConfigureAreaTabs(this.selectedConfigureAreaIds);
        },

        updateSides: function (configurableAreas, sides, currentSideId) {
            $.each(sides, function (i, side) {
                if (side.value_id == currentSideId) {
                    var areas = side.area;
                    $.each(configurableAreas, function (j, configurableArea) {
                        if (areas[configurableArea.id]) {
                            configurableArea.area = areas[configurableArea.id];
                        }
                    });
                }
            });
            return configurableAreas;
        },

        createConfigureAreaTabs: function (selectedConfigureAreaIds) {
            var self = this;
            $('div#grid_tab ul li').remove();
            $('div#grid_tab div').remove();
            // $("div#grid_tab").tabs("refresh");
            $("#grid_tab").tabs();
            if(self.currentSide && self.currentSide.image != undefined){
                var currentSide = self.currentSide;
            }
            var configurableAreas = this.configurableAreas;
            var updateValues = true;
            $.each(selectedConfigureAreaIds, function (i, selectedConfigureAreaId) {
                $.each(configurableAreas, function (i, configurableArea) {
                    if (configurableArea.id == selectedConfigureAreaId) {
                        $('#grid_tab ul').append('<li class="ui-state-default ui-corner-top" ><a class="ui-tabs-anchor" href="#' + configurableArea.id + '">' + configurableArea.title + '</a></li>');
                        var div = '<div id="' + configurableArea.id + '" class="ui-tabs-panel ui-widget-content ui-corner-bottom" style="display:none">' +
                            '<fieldset class="admin__fieldset">' +
                            '<div class="admin__field">' +
                            '<div class="admin__field-control">' +
                            '<div class="admin__control-table-wrapper">' +
                            '<form id="configure_area_' + configurableArea.id + '" name="configure_area">' +
                            '<table class="admin__dynamic-rows admin__control-table">' +
                            '<thead>' +
                            '<tr>' +
                            '<th>X</th>' +
                            '<th>Y</th>' +
                            '<th>Width</th>' +
                            '<th>Height</th>' +
                            '<th>Output Width</th>' +
                            '<th>Output Height</th>' +
                            '<th></th>' +
                            '</tr>' +
                            '</thead>' +
                            '<tbody>' +
                            '<tr class="data-row">' +
                            '<td>' +
                            '<div class="admin__field-control">' +
                            '<input class="admin__control-text" name="pos_x" type="text">' +
                            '<input class="admin__control-text" name="configarea_id" type="hidden" value="' + configurableArea.id + '">' +
                            '<input class="admin__control-text" name="side_id" type="hidden" value="' + self.currentSide.value_id + '">' +
                            '</div>' +
                            '</td>' +
                            '<td>' +
                            '<div class="admin__field-control">' +
                            '<input class="admin__control-text" name="pos_y" type="text">' +
                            '</div>' +
                            '</td>' +
                            '<td>' +
                            '<div class="admin__field-control">' +
                            '<input class="admin__control-text" name="width" type="text">' +
                            '</div>' +
                            '</td>' +
                            '<td>' +
                            '<div class="admin__field-control">' +
                            '<input class="admin__control-text" name="height" type="text">' +
                            '</div>' +
                            '</td>' +
                            '<td>' +
                            '<div class="admin__field-control">' +
                            '<input class="admin__control-text" name="output_width" type="text">' +
                            '</div>' +
                            '</td>' +
                            '<td>' +
                            '<div class="admin__field-control">' +
                            '<input class="admin__control-text" name="output_height" type="text">' +
                            '</div>' +
                            '</td>' +
                            '<td>' +
                            '<div class="admin__field-control">' +
                            '<button class="action-primary" type="button"><span>Save</span></button>' +
                            '</div>' +
                            '</td>' +
                            '</tr>' +
                            '</tbody>' +
                            '</table>' +
                            '</form>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</fieldset>' +
                            '<canvas id="canvas_' + configurableArea.id + '" height="500" width="500">' +
                            '</div>';
                        $('#grid_tab').append(div);
                        $("#grid_tab").tabs();
                        //configArea(configurableArea);

                        createConfigureArea('canvas_' + configurableArea.id, configurableArea, currentSide, updateValues, self);
                        updateValues = false;

                        var pos_x = $("#configure_area_" + configurableArea.id + " :input[name=pos_x]");
                        var pos_y = $("#configure_area_" + configurableArea.id + " :input[name=pos_y]");
                        var width = $("#configure_area_" + configurableArea.id + " :input[name=width]");
                        var height = $("#configure_area_" + configurableArea.id + " :input[name=height]");
                        var output_width = $("#configure_area_" + configurableArea.id + " :input[name=output_width]");
                        var output_height = $("#configure_area_" + configurableArea.id + " :input[name=output_height]");

                        pos_x.on("input", {self:self},  self.setRect);
                        pos_y.on("input", {self:self},  self.setRect);
                        width.on("input", {self:self},  self.setRect);
                        height.on("input", {self:self},  self.setRect);
                        output_width.on("input", {self:self},  self.setRect);
                        output_height.on("input", {self:self},  self.setRect);
                    }
                });
            });

            $("#grid_tab").tabs('refresh');
            $("#grid_tab").tabs("option", "active", 0);

            $("#grid_tab").tabs({
                activate: function (event, ui) {
                    activeTabId = getActiveTabId();
                    rect = self.rectArray[activeTabId - 1];
                    setCoordinates(null, self);
                }
            });

            activeTabId = getActiveTabId();

            $('form button[type=button]').click(function () {
                self.saveDesignArea(this.form);
            });
        },
        setRect: function (evt) {
            // alert(evt.target.value);
            var self = evt.data.self;
            var activeTabId = getActiveTabId();
            var rect = self.rectArray[activeTabId - 1];
            var canvas = self.canvasArray[activeTabId - 1];
            var prod_image = self.prodImageArray[activeTabId - 1];

            var width = $("#configure_area_" + activeTabId + " :input[name=width]");
            var height = $("#configure_area_" + activeTabId + " :input[name=height]");
            var output_width = $("#configure_area_" + activeTabId + " :input[name=output_width]");
            var output_height = $("#configure_area_" + activeTabId + " :input[name=output_height]");
            if(evt.target.name == "pos_x"){
                rect.set({ left: parseFloat(evt.target.value) + parseFloat(prod_image.get("left")) });
            }else if(evt.target.name == "pos_y"){
                rect.set({ top: parseFloat(evt.target.value) + parseFloat(prod_image.get("left")) });
            }else if(evt.target.name == "width"){
                rect.set({ width: parseFloat(evt.target.value) / rect.scaleX });
                self.setOutputValues(activeTabId);
            }else if(evt.target.name == "height"){
                rect.set({ height: parseFloat(evt.target.value) / rect.scaleY });
                self.setOutputValues(activeTabId);
            }else if(evt.target.name == "output_width"){
                output_height.val(Number(evt.target.value * height.val()/ width.val()).toFixed(2) );
            }else if(evt.target.name == "output_height"){
                output_width.val(Number(evt.target.value * width.val() / height.val()).toFixed(2)  );
            }
            canvas.renderAll();
        },
        setOutputValues: function(activeTabId){

            var width = $("#configure_area_" + activeTabId + " :input[name=width]");
            var height = $("#configure_area_" + activeTabId + " :input[name=height]");
            var output_width = $("#configure_area_" + activeTabId + " :input[name=output_width]");
            var output_height = $("#configure_area_" + activeTabId + " :input[name=output_height]");

            if(!parseFloat(output_width.val())) output_width.val(1);
            if(!parseFloat(output_height.val())) output_height.val(1);
            output_width.val(Number(parseFloat(output_height.val()) * parseFloat(width.val()) / parseFloat(height.val())).toFixed(2)  );
            output_height.val(Number(parseFloat(output_width.val()) * parseFloat(height.val())/ parseFloat(width.val())).toFixed(2) );
        },
        saveDesignArea: function (form) {

            var data = form.serialize().split("&");
            var obj={};
            for(var key in data)
            {
                if(typeof(data[key]) == "string" && data[key].indexOf("configarea_id") == -1 && data[key].indexOf("side_id" == -1  ) )
                    obj[data[key].split("=")[0]] = data[key].split("=")[1];
            }

            this.currentSide.area[activeTabId] = obj;
            $('body').trigger('processStart');
            $.ajax({
                url: this.saveUrl,
                type: 'POST',
                dataType: 'json',
                data: form.serialize(),
                complete: function (response) {
                    $('body').trigger('processStop');
                    if (response.responseText.isJSON()) {
                        var json = response.responseText.evalJSON();
                        alert({
                            title: '',
                            content: json.message
                        });
                    }
                },
                error: function (response) {
                    $('body').trigger('processStop');
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
    var activeTabId;
    var currentForm;
    var canvas;


    function getActiveTabId() {
        return $("#grid_tab .ui-tabs-panel:visible").attr("id");
    }


    function getActiveConfigAreaForm(ui) {
        //var activeTab = ui.newTab.attr("aria-controls");
        return currentForm = 'configure_area_' + activeTabId;
    }

    function createConfigureArea(canvasId, configurableArea, currentSide, updateValues, self) {
        var global_options = configurableArea;
        var canvas = new fabric.Canvas(canvasId, { width: 500, height: 500 });
        var area = {
            pos_x: 150,
            pos_y: 150,
            width: 150,
            height: 150
        }
        var activeTabId = getActiveTabId();
        if (currentSide.area[activeTabId]) {
            area = currentSide.area;
        } else if(global_options.area) {
            area = global_options.area;
        }
        self.canvasArray[Number(configurableArea.id)-1] = canvas;
        if(currentSide != undefined && currentSide.image.url != undefined){
            global_options.image = currentSide.image.url;
        }
        // "add" rectangle onto canvas

        fabric.Image.fromURL(global_options.image, function (oImg) {
            var prod_image = oImg;
            self.prodImageArray[Number(configurableArea.id)-1] = prod_image;

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
            canvas.setBackgroundImage(oImg);
            oImg.set('selectable', false); // make object unselectable

            var rect = new fabric.Rect({
                left: parseFloat(prod_image.get("left")) + parseFloat(area.pos_x),
                top: parseFloat(prod_image.get("top")) + parseFloat(area.pos_y),
                fill: '',
                stroke: "red",
                width: parseFloat(area.width) || 150,
                config_class: self,
                height: parseFloat(area.height) || 150
            });
            canvas.add(rect);
            canvas.setActiveObject(rect);
            self.rectArray[Number(configurableArea.id)-1] = rect;

            // canvas.on('after:render', setCoordinates);
            canvas.on('object:selected', setCoordinates);
            canvas.on('object:moving',  setCoordinates);
            canvas.on('object:scaling',  setCoordinates);
            if (updateValues)
                setCoordinates(null, self);
        });
    }
    function setCoordinates(event, self) {
        var currentFormId = getActiveConfigAreaForm();

        var pos_x = $("#" + currentFormId + " :input[name=pos_x]");
        var pos_y = $("#" + currentFormId + " :input[name=pos_y]");
        var width = $("#" + currentFormId + " :input[name=width]");
        var height = $("#" + currentFormId + " :input[name=height]");
        var output_width = $("#" + currentFormId + " :input[name=output_width]");
        var output_height = $("#" + currentFormId + " :input[name=output_height]");
        var currentConfigureAreaRect;
        activeTabId = getActiveTabId();
        if (event && event.target) {
            self = event.target.config_class;
            currentConfigureAreaRect = event.target;
        } else {
            var rect = self.rectArray[activeTabId - 1];
            currentConfigureAreaRect = rect;
            if(self.currentSide.area[activeTabId]){
                output_width.val(self.currentSide.area[activeTabId].output_width);
                output_height.val(self.currentSide.area[activeTabId].output_height);
            }
        }
        var prod_image = self.prodImageArray[activeTabId - 1];
        pos_x.val(Number(Math.round(currentConfigureAreaRect.left) - parseFloat(prod_image.get("left"))).toFixed(2));
        pos_y.val(Number(Math.round(currentConfigureAreaRect.top) - parseFloat(prod_image.get("top"))).toFixed(2));
        width.val(Number(Math.round(currentConfigureAreaRect.width) * currentConfigureAreaRect.scaleX).toFixed(2));
        height.val(Number(Math.round(currentConfigureAreaRect.height) * currentConfigureAreaRect.scaleY).toFixed(2));
        self.setOutputValues(activeTabId);
    }
});