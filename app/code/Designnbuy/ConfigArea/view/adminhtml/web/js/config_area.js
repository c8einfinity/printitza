define([
    "jquery",
    'Magento_Ui/js/modal/alert',
    "fabric"
], function($, alert, fabric) {
'use strict';
var saveBtn = $("#save_configarea");
var global_options;
var rect;
var prod_image;
var canvas;
saveBtn.on("click", saveDesignArea)
return function(options) {
    global_options = options;
    // create a wrapper around native canvas element (with id="c")
    
    canvas = new fabric.Canvas('config_canvas');
    // create a rectangle object
    var area = {
        pos_x: 150,
        pos_y: 150,
        width: 150,
        height: 150
    }
    
    if(global_options.area){
        area = JSON.parse(global_options.area);
    }
    
    //change color of container
    $(".canvas-container").css('background','#e3e3e3');
    //
    
    // "add" rectangle onto canvas
if(global_options.image){
    fabric.Image.fromURL(global_options.image, function (oImg) {
        prod_image = oImg;
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
        oImg.set("scaleX",imagewidth / oImg.width);
        oImg.set("scaleY",imageheight / oImg.height);
        oImg.set("left", (500 - imagewidth) / 2 );
        oImg.set("top", (500 - imageheight) / 2 );
        // canvas.add(oImg);
        canvas.setBackgroundImage(oImg);
        oImg.set('selectable', false); // make object unselectable
        
        
        
        rect = new fabric.Rect({
            left: parseFloat(prod_image.get("left")) + area.pos_x,
            top: parseFloat(prod_image.get("top")) + area.pos_y,
            fill: '',
            stroke: "red",
            width: area.width || 150,
            height: area.height || 150
        });
        canvas.add(rect);

        canvas.add(rect);
        canvas.setActiveObject(rect);


        canvas.on('object:selected', setCoordinates);
        canvas.on('object:moving',  setCoordinates);
        canvas.on('object:scaling', setCoordinates);
        setCoordinates(null, self);

        var currentFormId = getActiveConfigAreaForm();

        var pos_x = $("#" + currentFormId + " :input[name=pos_x]");
        var pos_y = $("#" + currentFormId + " :input[name=pos_y]");
        var width = $("#" + currentFormId + " :input[name=width]");
        var height = $("#" + currentFormId + " :input[name=height]");
        var output_width = $("#" + currentFormId + " :input[name=output_width]");
        var output_height = $("#" + currentFormId + " :input[name=output_height]");    

        pos_x.on("input", setRect);
        pos_y.on("input", setRect);
        width.on("input", setRect);
        height.on("input", setRect);
        output_width.on("input", setRect);
        output_height.on("input", setRect);
        
    });
    }
   
};

function setRect(evt) {

    var currentFormId = getActiveConfigAreaForm();
    
    var pos_x = $("#" + currentFormId + " :input[name=pos_x]");
    var pos_y = $("#" + currentFormId + " :input[name=pos_y]");
    var width = $("#" + currentFormId + " :input[name=width]");
    var height = $("#" + currentFormId + " :input[name=height]");
    var output_width = $("#" + currentFormId + " :input[name=output_width]");
    var output_height = $("#" + currentFormId + " :input[name=output_height]");

    if(evt.target.name == "pos_x"){
        rect.set({ left: parseFloat(evt.target.value) + parseFloat(prod_image.get("left")) });
    }else if(evt.target.name == "pos_y"){
        rect.set({ top: parseFloat(evt.target.value) + parseFloat(prod_image.get("left")) });
    }else if(evt.target.name == "width"){
        rect.set({ width: parseFloat(evt.target.value) / rect.scaleX });
        setOutputValues();
    }else if(evt.target.name == "height"){
        rect.set({ height: parseFloat(evt.target.value) / rect.scaleY });
        setOutputValues();
    }else if(evt.target.name == "output_width"){
        output_height.val(Number(parseFloat(evt.target.value) * parseFloat(height.val())/ parseFloat(width.val())).toFixed(2) );
    }else if(evt.target.name == "output_height"){
        output_width.val(Number(parseFloat(evt.target.value) * parseFloat(width.val()) / parseFloat(height.val())).toFixed(2)  );
    }
    canvas.renderAll();
}

function setOutputValues(){
    var currentFormId = getActiveConfigAreaForm();
    
    var width = $("#" + currentFormId + " :input[name=width]");
    var height = $("#" + currentFormId + " :input[name=height]");
    var output_width = $("#" + currentFormId + " :input[name=output_width]");
    var output_height = $("#" + currentFormId + " :input[name=output_height]");
    if(!parseFloat(output_width.val())) output_width.val(1);
    if(!parseFloat(output_height.val())) output_height.val(1);
    output_width.val(Number(parseFloat(output_height.val()) * parseFloat(width.val()) / parseFloat(height.val())).toFixed(2)  );
    output_height.val(Number(parseFloat(output_width.val()) * parseFloat(height.val())/ parseFloat(width.val())).toFixed(2) );    
}

function saveDesignArea(evt){
    var currentFormId = getActiveConfigAreaForm();
    var x = rect.get("left") - prod_image.get("left");
    var y = rect.get("top") - prod_image.get("top");
    var output_width = $("#" + currentFormId + " :input[name=output_width]");
    var output_height = $("#" + currentFormId + " :input[name=output_height]");
    var data = {
            pos_x: x,
            pos_y: y,
            width: rect.get("width") * rect.get("scaleX"),
            height: rect.get("height") * rect.get("scaleY"),
            output_width: output_width.val(),
            output_height: output_height.val(),
        };

    $.ajax({
        url: global_options.saveUrl,
        type: 'POST',
        dataType: 'json',
        data: { id: global_options.id, area:JSON.stringify(data)},
        complete: function(response) {

            if (response.responseText.isJSON()) {
                var json = response.responseText.evalJSON();
                alert({
                    title: '',
                    content: json.message,
                    actions: {
                        always: function(){
                            location.reload();
                        }
                    }
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
function setCoordinates (event) {
    var currentFormId = getActiveConfigAreaForm();
    
    var pos_x = $("#" + currentFormId + " :input[name=pos_x]");
    var pos_y = $("#" + currentFormId + " :input[name=pos_y]");
    var width = $("#" + currentFormId + " :input[name=width]");
    var height = $("#" + currentFormId + " :input[name=height]");
    var output_width = $("#" + currentFormId + " :input[name=output_width]");
    var output_height = $("#" + currentFormId + " :input[name=output_height]");

    var currentConfigureAreaRect;
    var activeTabId = 0;
    
    if (event && event.target) {
        currentConfigureAreaRect = event.target;
    } else {
        currentConfigureAreaRect = rect;
        var area = JSON.parse(global_options.area);
        if(area){
            output_width.val(area.output_width);
            output_height.val(area.output_height);
        }
    }
    
    pos_x.val(Number(Math.round(currentConfigureAreaRect.left) - parseFloat(prod_image.get("left"))).toFixed(2));
    pos_y.val(Number(Math.round(currentConfigureAreaRect.top) - parseFloat(prod_image.get("top"))).toFixed(2));
    width.val(Number(Math.round(currentConfigureAreaRect.width) * currentConfigureAreaRect.scaleX).toFixed(2));
    height.val(Number(Math.round(currentConfigureAreaRect.height) * currentConfigureAreaRect.scaleY).toFixed(2));
    setOutputValues();
}
function getActiveConfigAreaForm(ui) {
    //var activeTab = ui.newTab.attr("aria-controls");
    return 'configure_area';
}
});