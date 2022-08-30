define([ 'jquery', 'quill', 'raphael','jpicker','simplecolor','jscolor','jquery-cropper','canvas-to-blob'],
function($,Quill){
		if (colorJson)
			colorlist = jQuery.parseJSON(colorJson);
		hexCodeList = [];
		if (colorPickerType == "Printable") {
			jQuery(colorlist).each(function (i, val) {
				hexCodeList.push(val.colorCode.substring(1));
			});
		}
		var $sidebar = jQuery('.media'),
			$window = jQuery(window),
			offset = $sidebar.offset(),
			topPadding = 15;
		var areaNameAry = ["frontArea", "backArea", "leftArea", "rightArea"];
		maxheight = jQuery('.product-essential').outerHeight();
		boxWidth = jQuery('.product-essential').outerWidth();
		sliderheight = jQuery('.product-img-box').outerHeight();
		var addToCartForm = jQuery('#product_addtocart_form');
		if (maxheight > 750 && boxWidth > 720) {
			/*$window.scroll(function () {
				if ($window.scrollTop() > offset.top && $window.scrollTop() < Math.abs(maxheight - sliderheight) + offset.top - 80) {
					$sidebar.stop().animate({
						marginTop: $window.scrollTop() - offset.top
					});
				} else if ($window.scrollTop() == 0) {
					$sidebar.stop().animate({
						marginTop: '0'
					});
				} else if ($window.scrollTop() > Math.abs(maxheight - sliderheight) + offset.top - 80) {
					$sidebar.stop().animate({
						marginTop: (maxheight - sliderheight) - 80
					});
				}
			});*/
		}
		var marginSVG = '<svg id="bleedHolder" width="604.8000480651855" height="362.88002883911133" overflow="hidden" xmlns="http://www.w3.org/2000/svg"  xmlns:xlink="http://www.w3.org/1999/xlink"><rect id="bleedMarginRect" x="1" y="1" width="603.3000480651855" height="361.38002883911133" stroke="#000000" stroke-width="1" stroke-dasharray="5,5" opacity="1" fill="none" style="pointer-events:none"/><rect id="bleedMarginLabelBg" x="1" y="1" width="72" height="13" stroke="none" stroke-width="1" opacity="1" fill="#000000" style="pointer-events:none"/><text id="bleedMarginLabel" x="4" y="12" opacity="1" fill="#FFFFFF" style="pointer-events:none">Bleed Margin</text><rect id="cutMarginRect" x="20.16000160217285" y="20.16000160217285" width="564.4800448608398" height="322.5600256347656" stroke="#FF0000" stroke-width="1" stroke-dasharray="5,5" opacity="1" fill="none" style="pointer-events:none"/><rect id="cutMarginLabelBg" x="523.6400464630127" y="20.16000160217285" width="61" height="13" stroke="none" stroke-width="1" opacity="1" fill="#FF0000" style="pointer-events:none"/><text id="cutMarginLabel" x="528.6400464630127" y="31.16000160217285" opacity="1" fill="#FFFFFF" style="pointer-events:none">Cut Margin</text><rect id="safeMarginRect" x="40.3200032043457" y="40.3200032043457" width="524.1600416564941" height="282.2400224304199" stroke="#009900" stroke-width="1" stroke-dasharray="5,5" opacity="1" fill="none" style="pointer-events:none"/><rect id="safeMarginLabelBg" x="40.3200032043457" y="309.3200032043457" width="66" height="13" stroke="none" stroke-width="1" opacity="1" fill="#009900" style="pointer-events:none"/><text id="safeMarginLabel" x="43.3200032043457" y="317.3200032043457" opacity="1" fill="#FFFFFF" style="pointer-events:none">Safe Margin</text></svg>';

		var XLINKNS = 'http://www.w3.org/1999/xlink';
		var SVGNS = 'http://www.w3.org/2000/svg';
		var visElems = 'circle,ellipse,line,path,polygon,polyline,rect,text,image,tspan';
		/** variables for product tool/ */
		var bgRectSvg = '<svg style="pointer-events:none" overflow="visible" y="0" x="0" height="0" width="0" id="canvasBackground"><rect style="pointer-events:none" fill="none" stroke="#000000" y="0" x="0" height="100%" width="100%"></rect></svg>';

		var productImageHolderSvg = '<div id="product-image" style="text-align:center;"><svg id="productSvg" height="485px" width="400px" viewBox="0 0 400 485" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">' +
			'<defs>                  <filter id="colorMat" color-interpolation-filters="sRGB">                    <feColorMatrix values="0.8627450980392157 0 0 0 0 0 0.0784313725490196 0 0 0 0 0 0.23529411764705882 0 0 0 0 0 1 0" type="matrix" id="feColorMatrix"></feColorMatrix>                  </filter>                </defs>			' +
			'</svg>';
		var productImageSvg = '<image class="main_image" style="pointer-events:none;display:Block;" xlink:href=""></image>';
		/** variables for product tool/ */
		var typeMap_ = [];
		var FontModule;
		var imageEffect;
		var multistyleText;
		var specialChar;
		var totObjects = 0;//to handle getNextId, we will update this on side change in setSvgString
		var totalSides = ['front', 'back', 'left', 'right', 'top', 'bottom', 'insideleft', 'insideright','front', 'back', 'left', 'right', 'top', 'bottom', 'insideleft', 'insideright','front', 'back', 'left', 'right', 'top', 'bottom', 'insideleft', 'insideright'];
		var langData={
			"en_GB":{
				"adjustImage":"Adjust Image",
				"dontAdjust":"Cancel",
				"adjust":"Crop",
				"update":"Update",
				"edit":"Edit",
				"moveRight":"Move Right",
				"moveLeft":"Move Left",
				"moveDown":"Move Down",
				"moveUp":"Move Up",
				"increaseSize":"Increase Size",
				"decreaseSize":"Decrease Size",
				"changeFontFamily":"Change Font Family",
				"resetImage":"Reset Image",
				"view_front":"View Front",
				"view_back":"View Back",
				"bleed_margin":"Bleed Margin",
				"cut_margin":"Cut Margin",
				"safe_margin":"Safe Margin",
				"changeColor":"Click To Open Colorpicker",
				"front":"View Front",
				"back":"View Back",
				"left":"View Left",
				"right":"View Right",
				"charlimit":"Character limit is ",
				"removewhitelabel":"Remove White",
				"filterlabel": {
					"posterize": "Posterize",
					"css_brightness": "Brightness",
					"brightness_threshold": "Threshold",
					"css_invert": "Invert",
					"gamma_correct": "Color Correct",
					"gamma_correct2": "Gamma Correct",
					"css_grayscale": "Grayscale",
					"css_sharpen": "Sharpen",
					"css_hue_rotate": "Hue",
					"luminance_mask": "Luminance",
					"css_sepia": "Sepia",
					"dusk": "Dusk",
					"oneColor": "Monotone",
					"twoColor": "Duotone",
					"original": "None"
				},
				"localization": /* alter these to change the text presented by the picker (e.g. different language) */
				{
					pickerTitle:'Click To Open Color Picker.',
					text:
					{
						title: 'Drag Markers To Pick A Color',
						newColor: 'new',
						currentColor: 'current',
						ok: 'OK',
						cancel: 'Cancel'
					},
					tooltips:
					{
						colors:
						{
							newColor: 'New Color - Press &ldquo;OK&rdquo; To Commit',
							currentColor: 'Click To Revert To Original Color'
						},
						buttons:
						{
							ok: 'Commit To This Color Selection',
							cancel: 'Cancel And Revert To Original Color'
						},
						hue:
						{
							radio: 'Set To &ldquo;Hue&rdquo; Color Mode',
							textbox: 'Enter A &ldquo;Hue&rdquo; Value (0-360&deg;)'
						},
						saturation:
						{
							radio: 'Set To &ldquo;Saturation&rdquo; Color Mode',
							textbox: 'Enter A &ldquo;Saturation&rdquo; Value (0-100%)'
						},
						value:
						{
							radio: 'Set To &ldquo;Value&rdquo; Color Mode',
							textbox: 'Enter A &ldquo;Value&rdquo; Value (0-100%)'
						},
						red:
						{
							radio: 'Set To &ldquo;Red&rdquo; Color Mode',
							textbox: 'Enter A &ldquo;Red&rdquo; Value (0-255)'
						},
						green:
						{
							radio: 'Set To &ldquo;Green&rdquo; Color Mode',
							textbox: 'Enter A &ldquo;Green&rdquo; Value (0-255)'
						},
						blue:
						{
							radio: 'Set To &ldquo;Blue&rdquo; Color Mode',
							textbox: 'Enter A &ldquo;Blue&rdquo; Value (0-255)'
						},
						alpha:
						{
							radio: 'Set To &ldquo;Alpha&rdquo; Color Mode',
							textbox: 'Enter A &ldquo;Alpha&rdquo; Value (0-100)'
						},
						hex:
						{
							textbox: 'Enter A &ldquo;Hex&rdquo; Color Value (#000000-#ffffff)',
							alpha: 'Enter A &ldquo;Alpha&rdquo; Value (#00-#ff)'
						}
					}
				}
			},
			"fr_FR":{
				"adjustImage":"Ajuster l'image",
				"dontAdjust":"Ne pas ajuster",
				"adjust":"Régler",
				"update":"Update",
				"edit":"Edit",
				"moveRight":"Déplacer vers la droite",
				"moveLeft":"Se déplacer à gauche",
				"moveDown":"Descendre",
				"moveUp":"Déplacer vers le haut",
				"increaseSize":"Augmenter la taille",
				"decreaseSize":"Réduire la taille",
				"changeFontFamily":"Change famille de polices",
				"resetImage":"Réinitialiser l'image",
				"view_front":"Voir le devant",
				"view_back":"Voir Retour",
				"bleed_margin":"Marge de purge",
				"cut_margin":"Marge de Cut",
				"safe_margin":"marge de sécurité",
				"changeColor":"Cliquez pour ouvrir Colorpicker",
				"front":"Voir le devant",
				"back":"Voir Retour",
				"left":"Voir à gauche",
				"right":"Voir à droite",
				"charlimit":"La limite de caractères est ",
				"removewhitelabel":"Remove White",//Remove White
				"filterlabel": {
					"posterize": "Posterize",
					"css_brightness": "Luminosité",//Brightness
					"brightness_threshold": "Seuil",//Threshold
					"css_invert": "Inverser",//Invert
					"gamma_correct": "Color Correct",//Color Correct
					"gamma_correct2": "Correction Gamma",//Gamma Correct
					"css_grayscale": "Grayscale",//Grayscale
					"css_sharpen": "affiler",//Sharpen
					"css_hue_rotate": "Teinte",//Hue
					"luminance_mask": "Luminance",//Luminance
					"css_sepia": "Sépia",//Sepia
					"dusk": "Crépuscule",//Dusk
					"oneColor": "Monotone",//Monotone
					"twoColor": "bichromie",//DuoTone
					"original": "Aucun"//None
				},
				"localization": /* alter these to change the text presented by the picker (e.g. different language) */
				{
					pickerTitle:'Cliquez pour ouvrir Colorpicker',
					text: {
						title: "Drag Markers To Pick A Color",
						newColor: "nouveau",//new
						currentColor: "courant",//current
						ok: "D'accord",//OK
						cancel: "annuler"//Cancel
					},
					tooltips: {
						colors: {
							newColor: "Nouvelle couleur - Presse & ldquo; OK & rdquo; pour engager",//New Color - Press &ldquo;OK&rdquo; To Commit
							currentColor: "Cliquez pour revenir à la couleur d'origine"//Click To Revert To Original Color
						},
						buttons: {
							ok: "Engagez-vous à cette sélection Couleur",//Commit To This Color Selection
							cancel: "Annuler et revenir à la couleur d'origine"//Cancel And Revert To Original Color
						},
						hue: {
							radio: "Défini sur & ldquo; Hue & rdquo; mode couleur",//Set To &ldquo;Hue&rdquo; Color Mode
							textbox: "Entrez A & ldquo; Hue & rdquo; Valeur (0-360 & deg;)"//Enter A &ldquo;Hue&rdquo; Value (0-360&deg;)
						},
						saturation: {
							radio: "Défini sur & ldquo; Saturation & rdquo; mode couleur",//Set To &ldquo;Saturation&rdquo; Color Mode
							textbox: "Entrez A & ldquo; Saturation & rdquo; Valeur (0-100%)"//Enter A &ldquo;Saturation&rdquo; Value (0-100%)
						},
						value: {
							radio: "Défini sur & ldquo; Valeur & rdquo; mode couleur",//Set To &ldquo;Value&rdquo; Color Mode
							textbox: "Entrez A & ldquo; Valeur & rdquo; Valeur (0-100%)"//Enter A &ldquo;Value&rdquo; Value (0-100%)
						},
						red: {
							radio: "Défini sur & ldquo; Red & rdquo; mode couleur",//Set To &ldquo;Red&rdquo; Color Mode
							textbox: "Entrez A & ldquo; Red & rdquo; Valeur (0-255)"//Enter A &ldquo;Red&rdquo; Value (0-255)
						},
						green: {
							radio: "Défini sur & ldquo; Green & rdquo; mode couleur",//Set To &ldquo;Green&rdquo; Color Mode
							textbox: "Entrez A & ldquo; Green & rdquo; Valeur (0-255)"//Enter A &ldquo;Green&rdquo; Value (0-255)
						},
						blue: {
							radio: "Défini sur & ldquo; Blue & rdquo; mode couleur",//Set To &ldquo;Blue&rdquo; Color Mode
							textbox: "Entrez A & ldquo; Blue & rdquo; Valeur (0-255)"//Enter A &ldquo;Blue&rdquo; Value (0-255)
						},
						alpha: {
							radio: "Défini sur & ldquo; Alpha & rdquo; mode couleur",//Set To &ldquo;Alpha&rdquo; Color Mode
							textbox: "Entrez A & ldquo; Alpha & rdquo; Valeur (0-100)"//Enter A &ldquo;Alpha&rdquo; Value (0-100)
						},
						hex: {
							textbox: "Entrez A & ldquo; Hex & rdquo; Couleur de la valeur (# 000000- # ffffff)",//Enter A &ldquo;Hex&rdquo; Color Value (#000000-#ffffff)
							alpha: "Entrez A & ldquo; Alpha & rdquo; Valeur (# 00- # FF)"//Enter A &ldquo;Alpha&rdquo; Value (#00-#ff)
						}
					}
				}
			}
		}
// var currentStore = "fr_FR";


		var Margin = {

			_bleedMargin: {
				top: 0.125,
				right: 0.125,
				bottom: 0.125,
				left: 0.125,
			},
			_safeMargin: {
				top: 0.125,
				right: 0.125,
				bottom: 0.125,
				left: 0.125,
			},

			get horizontalBleed() {
				var left = Margin._bleedMargin.left;
				var right = Margin._bleedMargin.right;
				return left + right;
			},
			get verticalBleed() {
				var top = Margin._bleedMargin.top;
				var bottom = Margin._bleedMargin.bottom;
				return top + bottom;
			},
			get horizontalBleedAbs() {
				var left = (Margin._bleedMargin.left > 0) ? Margin._bleedMargin.left : 0;
				var right = (Margin._bleedMargin.right > 0) ? Margin._bleedMargin.right : 0;
				return left + right;
			},
			get verticalBleedAbs() {
				var top = (Margin._bleedMargin.top > 0) ? Margin._bleedMargin.top : 0;
				var bottom = (Margin._bleedMargin.bottom > 0) ? Margin._bleedMargin.bottom : 0;
				return top + bottom;
			},
			get horizontalSafeAbs() {
				var left = (Margin._safeMargin.left > 0) ? Margin._safeMargin.left : 0;
				var right = (Margin._safeMargin.right > 0) ? Margin._safeMargin.right : 0;
				return left + right;
			},
			get verticalSafeAbs() {
				var top = (Margin._safeMargin.top > 0) ? Margin._safeMargin.top : 0;
				var bottom = (Margin._safeMargin.bottom > 0) ? Margin._safeMargin.bottom : 0;
				return top + bottom;
			},
			get horizontalSafe() {
				return Margin._safeMargin.left + Margin._safeMargin.right;
			},
			get verticalSafe() {
				return Margin._safeMargin.top + Margin._safeMargin.bottom;
			},
			get bleedRect() {
				return Margin._bleedMargin.left + Margin._bleedMargin.right;
			},
			get bleedMargin() {
				return Margin._bleedMargin;
			},
			set bleedMargin(val) {
				Margin._bleedMargin.top = parseFloat(val[0]);
				Margin._bleedMargin.right = parseFloat(val[1]);
				Margin._bleedMargin.bottom = parseFloat(val[2]);
				Margin._bleedMargin.left = parseFloat(val[3]);
			},
			get safeRect() {
				return Margin._bleedMargin.left + Margin._bleedMargin.right;
			},
			get safeMargin() {
				return Margin._safeMargin;
			},
			set safeMargin(val) {
				Margin._safeMargin.top = parseFloat(val[0]);
				Margin._safeMargin.right = parseFloat(val[1]);
				Margin._safeMargin.bottom = parseFloat(val[2]);
				Margin._safeMargin.left = parseFloat(val[3]);
			}
		}

		function isIE(){
			return navigator.userAgent.indexOf('MSIE') >= 0 || !!navigator.userAgent.match(/Trident.*rv[ :]*11\./);
		}
		function Object_to_ary(obj){
			Object.keys(obj).map(function(e) {
				return obj[e]
			})
		}
		
		function isTouch() {
			try {
				document.createEvent("TouchEvent");
				return true;
			} catch (e) {
				return false;
			}
		}

		

		function setLoaderPos() {
			// var elem = $('loading_mask_loader');
			// elem.show('loading_mask_loader');
		}

		function createPicker(elem) {
			var color = elem.attr("color");
			console.log("color");
			console.log(color);
			if (colorPickerType == "Printable" || colorPickerType.toString().toLowerCase() == "onecolor") {
				elem.simpleColor({
					boxHeight: 40,
					cellWidth: 20,
					cellHeight: 20,
					columns: 10,
					defaultColor: color,
					colors: hexCodeList,
					chooserCSS: { 'left': '-228px', 'z-index': '1' },
					displayCSS: { 'width': '20px', 'height': '20px' },
					displayColorCode: false,
					livePreview: false,
					onSelect: function (hex, element) {
						changeColor(element[0], "#" + hex);
					}/*,
					 onCellEnter: function(hex, element) {
					 console.log("You just entered #" + hex + " for input #" + element.attr('class'));
					 },
					 onClose: function(element) {
					 alert("color chooser closed for input #" + element.attr('class'));
					 }*/
				});
			} else {

				var clrBtn = jQuery('<input value="'+color+'" type="button" class="btn blueBtn jscolor" />');
				elem.append(clrBtn);

				var jsc = new jscolor(clrBtn[0]);
				jsc.onFineChange = function(){changeColor(this.valueElement.parentNode, "#"+this.valueElement.value);}

				/*elem.jPicker(
				 {
				 window:
				 {
				 expandable: true,
				 alphaSupport: false,
				 position: {
				 x: 'screenCenter', // acceptable values "left", "center", "right", "screenCenter", or relative px value (-545)
				 y: '0', // acceptable values "top", "bottom", "center", or relative px value
				 }
				 },
				 color:
				 {
				 active: new jQuery.jPicker.Color({ hex: color })
				 },
				 localization: langData[currentStore].localization,
				 },
				 function (color, context) {
				 var all = color.val('all');
				 var hex = (all && '#' + all.hex || 'none');
				 changeColor(this, hex);
				 }
				 );*/
			}


		}

		function getStrokeWidth(elem) {
			if (elem.childNodes.length) {
				if (elem.childNodes[0].hasAttribute("stroke-width")) {
					return parseInt(elem.childNodes[0].getAttribute("stroke-width"));
				} else if (elem.childNodes[0].hasAttribute("stroke")) {
					var stroke = elem.childNodes[0].getAttribute("stroke");
					if (stroke != "none" && stroke != "null") {
						return 1;
					} else {
						return 0;
					}
				} else {
					return 0;
				}
			} else {
				if (elem.tagName && elem.hasAttribute("stroke-width")) {
					return parseFloat(elem.getAttribute("stroke-width"));
				} else if (elem.hasAttribute("stroke")) {
					var stroke = elem.getAttribute("stroke");
					if (stroke != "none" && stroke != "null") {
						return 1;
					} else {
						return 0;
					}
				} else {
					return 0;
				}

			}
		}
		function getStrokeColor(textElem) {
			if (textElem.childNodes.length) {
				if (textElem.childNodes[0].getAttribute("stroke")) {
					return textElem.childNodes[0].getAttribute("stroke");
				} else if (textElem.getAttribute("stroke")) {
					return textElem.getAttribute("stroke");
				} else {
					return "#000000";
				}
			} else {
				if (textElem.getAttribute("stroke")) {
					return textElem.getAttribute("stroke");
				} else {
					return "#000000";
				}
			}
		}
		function getFillColor(textElem) {
			if (textElem.childNodes.length) {
				if (textElem.childNodes[0].getAttribute("fill")) {
					return textElem.childNodes[0].getAttribute("fill");
				} else if (textElem.getAttribute("fill")) {
					return textElem.getAttribute("fill");
				} else {
					return "#000000";
				}
			} else {
				if (textElem.getAttribute("fill")) {
					return textElem.getAttribute("fill");
				} else {
					return "#000000";
				}
			}
		}

		function changeColor(btn, color) {

			var textElem = jQuery("#" + btn.getAttribute("elemid"))[0];
			if(textElem.getAttribute("sfid")){
				updateSmartFieldValuesOnCurrentPage(textElem.getAttribute("sfid"), "color", color, btn)
			}else{
				if(textElem.getAttribute("type") == "textarea"){
					textElem.childNodes[0].childNodes[0].setAttribute("fill", color);
				}else if(textElem.getAttribute("type") == "advance"){
					multistyleText.changeColor(color);
				}else if(textElem.tagName == "text"){
					textElem.setAttribute("fill", color);
				}else{
					if (textElem.childNodes.length) {
						for (var i = 0; i < textElem.childNodes.length; i++) {
							textElem.childNodes[i].setAttribute("fill", color);
						}
					}
				}
				textElem.setAttribute("fill", color);
			}
			if ((colorPickerType == "Printable" && productData.onlySingleColor == 1) || colorPickerType.toString().toLowerCase() == "onecolor") {
				convertDesignColor(true,color);
				multistyleText.changeDefaultcolor(color);
			}
		}

		function highlightText(evt) {
			var btn = evt.target;
			var elem = jQuery("#" + btn.getAttribute("elemid"));
			elem.animate({
				opacity: 0,
			}, 500, function() {
				elem.animate({
					opacity: 1,
				}, 500, function() {

				});
			});
		}

		function getBoundingBox(elem) {
			if (elem.getAttribute("type") == "text") {
				var path = elem.childNodes[0];
				var newPath;
				if (elem.getAttribute("transform") && elem.getAttribute("transform").indexOf("rotate") > -1) {
					var transform = elem.getAttribute("transform");
					var startIndex = transform.indexOf("rotate");
					var endIndex = transform.indexOf(")", startIndex);
					var rotation = transform.substr(startIndex + 7, endIndex);
					var angle = rotation.split(" ");
					newPath = Raphael.transformPath(path.getAttribute("d"), "r" + angle);
					return Raphael.pathBBox(newPath);
				} else {
					//newPath = path.getAttribute("d");
					return elem.getBBox();
				}

			} else {
				return elem.getBBox();
			}
		}

		function checkOutOfBound(textarea, elem) {
			//var contentWidth = content.getAttribute("width");
			//var contentHeight = content.getAttribute("height");
			var cutMarginRect;
			if (toolType == "producttool") {
				cutMarginRect = jQuery('#canvasBackground')[0];
			} else {
				cutMarginRect = jQuery('#cutMarginRect')[0];
			}
			var boundToCheck = cutMarginRect.getBoundingClientRect();

			var elemBbox;
			if (elem.getAttribute("type") == "photobox") {
				elemBbox = elem.childNodes[1].getBoundingClientRect();
			} else if (elem.getAttribute("type") == "textarea") {
				elemBbox = elem.childNodes[1].getBoundingClientRect();
			} else {
				elemBbox = elem.getBoundingClientRect();
			}

			console.log("boundToCheck");
			console.log(boundToCheck);
			console.log("elemBbox");
			console.log(elemBbox);
			var children = jQuery(textarea).parents(".rowHolder").children();
			//console.log(children);
			var div = jQuery(children[children.length - 1]);
			//console.log("div");
			//console.log(div);
			if (elemBbox.left + elemBbox.width > boundToCheck.left + boundToCheck.width || elemBbox.top + elemBbox.height > boundToCheck.top + boundToCheck.height || elemBbox.left < boundToCheck.left || elemBbox.top < boundToCheck.top) {
				//textarea.style.backgroundColor = "#f48181"
				if (!div.hasClass("validation-warning")) {
					jQuery(textarea).parents(".rowHolder").append('<div style="" class="validation-warning">This field is out of design</div>');
				}
			} else {
				//var div = jQuery(textarea).parent().next();
				if (div.hasClass("validation-warning")) {
					div.remove();
				}
				//textarea.style.backgroundColor = "#FFFFFF"
			}
		}

		function disableControls(btn) {
			var row = jQuery(btn).parents(".rowHolder");
			row.children(".clrPicker").addClass("disabled");
			// row.children(".jPicker").addClass("disabled");
			row.children(".fontsizeButton").addClass("disabled");
			row.children(".directoinButton").addClass("disabled");
		}
		function enableControls(btn) {
			var row = jQuery(btn).parents(".rowHolder");
			row.children(".clrPicker").removeClass("disabled");
			// row.children(".jPicker").removeClass("disabled");
			row.children(".fontsizeButton").removeClass("disabled");
			row.children(".directoinButton").removeClass("disabled");
		}
		function editAdvanceText(e) {
			var elemid = e.currentTarget.getAttribute("elemid");
			var textareaid = e.currentTarget.getAttribute("textareaid");
			multistyleText.editText(jQuery("#"+elemid)[0], jQuery("#"+textareaid)[0]);
		}
		function updateText(evt) {
			var btn = evt.currentTarget;			
			var elem = jQuery("#" + btn.getAttribute("elemid"));
			if(jQuery.trim(evt.currentTarget.value)){
				if(elem.attr("type") == "textarea" ){
					addUpdateText(evt);
				}else if(elem[0].tagName == "text" ){
					addUpdateText(evt);
				}else{
					clearTimeout(updateTextInterval);
					updateTextInterval = setTimeout(addUpdateShapeText, 500, evt);
				}
			}else{
				if(elem.attr("type") == "textarea" ){

				}else{
					clearTimeout(updateTextInterval);
				}
				elem.hide();
				disableControls(btn);
			}
			if(elem.attr("isrequired") == "true" ){
				elem.attr("isrequired", "false");
			}
		}

		function addUpdateText(evt, elem, newText, upateSnartfield) {
			if(evt){
				var btn = evt.currentTarget;
				jQuery("#" + btn.getAttribute("elemid")).show();
				enableControls(btn);
			}
			var elem = elem || jQuery("#" + btn.getAttribute("elemid"))[0];
			var content =  newText || jQuery("#" + btn.getAttribute("textareaid"))[0].value;
			if(elem.getAttribute("type") == "textarea"){
				elem.setAttribute("text",content.replace(/"/g, "&quote;").replace(/\n/g, "@##@#@"));
				var text = elem.childNodes[0].childNodes[0];
				autoWrapText(text,content);
				alignVertical(elem);
			}else{
				elem.textContent = content;
				if(elem.hasAttribute("sfid") && upateSnartfield != false ){
					updateSmartFieldValue(elem.getAttribute("sfid"), content);
				}
			}
		}

		var fontSizeAry = [5, 6, 8, 10, 11, 12, 13, 14, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36, 38, 40, 42, 44, 46, 48, 50, 52, 54, 56, 58, 60, 62, 64, 66, 68, 70, 72, 74, 76, 78, 80, 82, 84, 86, 88, 90, 92, 94, 96, 98, 100, 102, 104, 106, 108, 110, 112, 114, 116, 118, 120, 122, 124, 126, 128, 130, 132, 134, 136, 138, 140, 142, 144];

		function getClosestSize(size){
			var len = fontSizeAry.length;
			var closest = size;
			while(len--){
				if(fontSizeAry[len] < size ){
					closest = fontSizeAry[len];
					break;
				}
			}
			return closest;
		}

		function autoWrapText(text,content){
			setTextInArea(text,content);
			if(text.parentNode.parentNode.getAttribute("autofit") == "true"){
				var textBbox = text.getBBox();
				var rect = text.parentNode.parentNode.childNodes[1];
				if((textBbox.width > parseFloat(rect.getAttribute("width")) || textBbox.height > parseFloat(rect.getAttribute("height")) )  ){
					var scalex = scaley = 1;
					if(textBbox.width > parseFloat(rect.getAttribute("width"))){
						scalex = parseFloat(rect.getAttribute("width")) / textBbox.width;
					}
					if(textBbox.height > parseFloat(rect.getAttribute("height"))){
						scaley = parseFloat(rect.getAttribute("height")) / textBbox.height;
					}
					var scale = Math.min(scalex,scaley);
					var curFontSize = parseInt(text.getAttribute("font-size"));
					var newfontSize = curFontSize * scale;
					newfontSize = getClosestSize(newfontSize);
					// console.log("scale");
					// console.log(scale);
					// console.log("newfontSize");
					// console.log(newfontSize);
					text.setAttribute("font-size", newfontSize);
					text.parentNode.parentNode.setAttribute("font-size", newfontSize);
					setTextInArea(text);
					// textBbox = text.getBBox();
				}
			}

		}

		function setTextInArea(text,content) {
			if(content == undefined) content = text.parentNode.parentNode.getAttribute("text").replace(/&quote;/g, '"').replace(/@##@#@/g, "\n");
			var lines = content.split("\n");
			jQuery(text).empty();
			jQuery.each(lines, function (i, line) {
				addLine(line, text);
			});
		}

		var bbox;
		function addLine(line, text){
			var width_to_check = text.parentNode.parentNode.childNodes[1].getAttribute("width");
			var lineHeight = text.getAttribute("lineHeight") ? text.getAttribute("lineHeight") : 1;
			var attr = {
				"id": getNextId()
				//"xml:space" : "preserve"
			};

			line = line + "";
			if (line.length == 0) {
				line = " ";
			}
			var tspanLen = jQuery(text).find("tspan").length;
			if (tspanLen > 0) {
				// console.log("tspanLen");
				// console.log(tspanLen);
				// console.log("bbox");
				// console.log(bbox);
				if(tspanLen == 1){
					bbox = text.getBBox();
				}
				//attr["dy"] = "1em";
				//alert(parseFloat(text.getAttribute('y'))+(bbox.height )*i);
				attr["x"] = jQuery(text).attr("x");
				attr["y"] = parseFloat(text.getAttribute('y')) + (bbox.height) * tspanLen * lineHeight;
			} else {
				attr["x"] = jQuery(text).attr("x");
				attr["y"] = jQuery(text).attr("y");

			}

			var span = document.createElementNS(SVGNS, "tspan");
			jQuery(span).attr(attr);

			jQuery(text).append(span);
			//create word array from line
			var words = line.split(" ");

			var newline = [];
			span.textContent = line;

			while(span.getComputedTextLength() > width_to_check && words.length>1 ){
				console.log("text.getComputedTextLength()");
				console.log(span.getComputedTextLength());
				newline.unshift(words.pop());
				span.textContent = words.join(" ");
			}
			console.log("newline");
			console.log(newline);
			if(newline.length){
				addLine(newline.join(" "),text);
			}
			span = null;
		}

		function addUpdateShapeText(evt, elem, newText) {
			if(evt){
				var btn = evt.currentTarget;
				jQuery("#" + btn.getAttribute("elemid")).show();
				enableControls(btn);
			}
			elem = elem || jQuery("#" + btn.getAttribute("elemid"))[0];
			newText = newText || jQuery("#" + btn.getAttribute("textareaid"))[0].value;

			var posX, posY, textGroup, font, fontSize, bbox, fill, stroke, path, shapeId, shapeVal, prevText, lineHeight, strokeWidth, strokeDasharray, rotationAngle, startPos;

			bbox = elem.getBBox();
			//bbox =  Raphael.pathBBox(elem.firstChild.getAttribute("d"))
			posX = bbox.x;
			posY = bbox.y;

			font = elem.getAttribute("font-family");
			fontSize = elem.getAttribute("font-size");
			fill = getFillColor(elem);

			prevText = elem.getAttribute("text");
			stroke = getStrokeColor(elem);
			align = elem.getAttribute("text-anchor");
			strokeDasharray = elem.getAttribute("stroke-dasharray");
			var fontWeight = elem.getAttribute("font-weight");
			var fontStyle = elem.getAttribute("font-style");

			shapeId = elem.getAttribute("shapeid");
			shapeVal = elem.getAttribute("shapeval");

			lineHeight = parseFloat(elem.getAttribute("lineheight"));
			if (!stroke || stroke == "null") {
				stroke = "none";
			} else if (stroke != "none" && stroke.indexOf("#") == -1) {
				stroke = "#" + stroke;
			}
			stroke = strPad(stroke.substring(1), 6, "0", true);
			stroke = "#" + stroke;
			//console.log("fill = " + fill);

			strokeWidth = getStrokeWidth(elem);
			//if(!strokeWidth){
			//strokeWidth = 1;
			//}
			var xScale = elem.getAttribute("xscale");
			var yScale = elem.getAttribute("yscale");
			if (yScale) {
				// lineHeight = lineHeight * yScale;
			}
			while (elem.childNodes.length) {
				elem.removeChild(elem.childNodes[0]);
			}
			paper.clear();

			var letters = paper.printArray(posX, posY, newText || elem.getAttribute("text").replace(/&quote;/g, '"').replace(/@##@#@/g, "\n"), font, fill, fontSize, align, fontWeight, fontStyle, null, lineHeight, elem.getAttribute("fixedWidth"), elem.getAttribute("yscale"));
			//console.log("letters");
			//console.log(letters);
			//console.log("strokeDasharray = " + strokeDasharray );
			if (fill.indexOf("#") == -1) {
				fill = "#" + fill;
			}
			for (var i = 0; i < letters.length; i++) {
				path = document.createElementNS(SVGNS, "path");
				path.setAttribute("id", "svg_" + i);
				path.setAttribute("fill", fill);
				path.setAttribute("stroke", stroke);
				path.setAttribute("stroke-width", strokeWidth);
				path.setAttribute("stroke-dasharray", strokeDasharray);
				//old{
				// path.setAttribute("d", letters[i].node.getAttribute("d"));
				// path.setAttribute("transform", letters[i].node.getAttribute("transform"));
				//}
				//new{ //removing transform
				var pathStr = letters[i].node.getAttribute("d");
				var str = letters[i].attrs["transform"];//"T29.419071197509766,186.7555694580078";
				var transform = 't'+str.substring(1);
				pathStr = Raphael.transformPath(pathStr, transform );
				path.setAttribute("d", pathStr);
				//}
				path.setAttribute("style", "pointer-events:inherit");
				elem.appendChild(path);
			}
			//console.log(shapeId);
			//console.log(shapeVal);
			if (letters.length == 1) {
				if (shapeId > 0 && shapeId < 9) {
					var pathNodes = letters[0].attrs.path;
					var pathBbox = letters[0].getBBox();
					//console.log(bbox);
					var pathOffset = getPathOffSet(letters[0].attrs.path);

					path.setAttribute("d", getWrappedPath(pathNodes, pathBbox, pathOffset, 1E5, parseInt(shapeId), parseFloat(shapeVal)).toString())
				} else if (shapeId == 9) {
					var pathBbox = letters[0].getBBox();
					if (shapeVal != 0)
						path.setAttribute("d", textCurve(shapeVal, pathBbox, newText, prevText, true, pathBbox, font, fontSize, 0, fill, fill, 0));
				}
			}

			var transform, newPath;
			//var tr = elem.getAttribute("transform");
			if(xScale || yScale){
				for(var i=0; i<elem.childNodes.length; i++){
					path = elem.childNodes[i];
					var	childBbox = path.getBBox();
					newPath = path.getAttribute('d');
					transform = 's'+xScale+','+yScale;
					newPath = Raphael.transformPath(newPath, transform );
					//elem.setAttribute("transform","translate("+bbox.x+","+bbox.y+") scale("+xScale+","+yScale+") translate("+bbox.x*-1+","+bbox.y*-1+") ");
					path.setAttribute('d', newPath);
					var childNewBbox = path.getBBox();
					var xdif = childBbox.x - childNewBbox.x;
					var ydif = childBbox.y - childNewBbox.y;
					if(xdif || ydif){
						newPath = path.getAttribute('d');
						transform = 't'+xdif+','+ydif;
						//console.log(transform);
						newPath = Raphael.transformPath(newPath, transform );
						path.setAttribute('d', newPath);
					}
				}
			}
			//set position of new textelem to old one
			var newBbox = elem.getBBox();
			var diffX, diffY;
			// diffX = bbox.x - newBbox.x;
			// diffY = bbox.y - newBbox.y;
			if(elem.getAttribute("text-anchor")=="start"){
				diffX =  bbox.x - newBbox.x;
				diffY =  bbox.y - newBbox.y;
			}else if(elem.getAttribute("text-anchor")=="middle"){
				diffX = (bbox.x + bbox.width/2) - (newBbox.x + newBbox.width/2);
				diffY = bbox.y - newBbox.y;
			}else{
				diffX = (bbox.x + bbox.width) - (newBbox.x + newBbox.width);
				diffY = bbox.y - newBbox.y;
			}
			for(var i=0; i<elem.childNodes.length; i++){
				path = elem.childNodes[i];
				newPath = path.getAttribute('d');
				transform = 't'+diffX+','+diffY;
				newPath = Raphael.transformPath(newPath, transform );
				path.setAttribute('d', newPath);
			}

			//setting alignment
			if(elem.childNodes.length > 1){
				var textElemBbox = elem.getBBox();
				var pathBbox;
				console.log("textElemBbox");
				console.log(textElemBbox);
				for(var i=0; i<elem.childNodes.length; i++){
					path = elem.childNodes[i];
					newPath = path.getAttribute('d');
					pathBbox = Raphael.pathBBox(newPath);
					console.log("pathBbox");
					console.log(pathBbox);
					if(elem.getAttribute("text-anchor")=="start"){
						diffX =  textElemBbox.x - pathBbox.x;
						diffY =  textElemBbox.y - pathBbox.y;
					}else if(elem.getAttribute("text-anchor")=="middle"){
						diffX = (textElemBbox.x + textElemBbox.width/2) - (pathBbox.x + pathBbox.width/2);
						diffY = textElemBbox.y - pathBbox.y;
					}else{
						diffX = (textElemBbox.x + textElemBbox.width) - (pathBbox.x + pathBbox.width);
						diffY = textElemBbox.y - pathBbox.y;
					}
					console.log("diffX");
					console.log(diffX);
					// console.log(diffY);
					transform = 't'+diffX+',0';
					newPath = Raphael.transformPath(newPath, transform );
					path.setAttribute('d', newPath);
				}
			}

			elem.setAttribute('text', newText);

			//var xScale = elem.getAttribute("xscale");
			//var yScale = elem.getAttribute("yscale");
			//if(xScale || yScale)
			//elem.setAttribute("transform","translate("+bbox.x+","+bbox.y+") scale("+xScale+","+yScale+") translate("+bbox.x*-1+","+bbox.y*-1+") ");
			if(evt)
				checkOutOfBound(evt.target, elem);
		}

		Raphael.fn.printArray = function printArray(x, y, Str, font, fontColor, fontSize, align, fontWeight, fontStyle, letter_spacing, line_height, fixedWidth, yScale) {
			var result = [],
				lineWidthAry = [],
				maxLineWidth = 0;
			fixedWidth = fixedWidth || Infinity;
			yScale = yScale || 1;
			fontSize = parseFloat(fontSize);
			//size = size || 16;
			fontColor = fontColor || "#000000";
			align = align || "start";
			fontWeight = (fontWeight == "bold") ? 700 : 400;
			fontStyle = (fontStyle == "italic") ? fontStyle : "normal";
			letter_spacing = letter_spacing || 0;
			line_height = line_height || 1;
			font = this.getFont(font, fontWeight, fontStyle);
			console.log("font");
			console.log(font);
			var bb = font.face.bbox.split(" ");
			var scale = (fontSize || 16) / font.face["units-per-em"];
			var lineHeight = (bb[3] - bb[1]) * scale;

			var memLines = Str.split("\n");
			//console.log(font);
			//console.log("Str");
			//console.log(Str);

			for (var i = 0; i < memLines.length; i++) {
				if (jQuery.trim(memLines[i])) {
					var glyph = this.print(0, 0, memLines[i], font, fontSize, null, letter_spacing, null, direction).attr({ opacity: 0 }).attr({ fill: fontColor });
					glyph.attr({ transform: "T" + x + "," + y, opacity: 1 });
					result.push(glyph);
					var bbox = glyph.getBBox();
					maxLineWidth = Math.max(bbox.width, maxLineWidth);
					lineWidthAry.push(bbox.width);
				}
				y += lineHeight * line_height * yScale;
			}
			//aligning lines

			if (result.length > 1) {
				var GlyphCount = 0;
				for (var i = 0; i < result.length; i++) {
					var diffX = 0;
					switch (align) {
						case "start":
							continue;
						case "middle":
							diffX = (maxLineWidth - lineWidthAry[i]) / 2;
							break;
						case "end":
							diffX = (maxLineWidth - lineWidthAry[i]);
							break;
					}
					var xPos = result[i].matrix.e + diffX;
					result[i].attr({ transform: "T" + xPos + "," + result[i].matrix.f, opacity: 1 });
				}
			}
			return result;
		}
		function createProductImage() {
			if(jQuery(".main_image").length){
				jQuery(".main_image").hide();
				jQuery("#img_"+parseInt(currentSide)).show();
			}else{
				var productImageHolder = jQuery(productImageHolderSvg);
				jQuery(content.parentNode).before(productImageHolder);
				var colors = productData.allColors.color;
				var color;

				if(colorId){
					color = colors[colorId];
				}else{
					color = colors[Object.keys(colors)[0]];
				}

				for (var i = 0; i < productData.noofSides; i++) {
					//productImage = jQuery(productImageSvg);
					var productImage = document.createElementNS(SVGNS, 'image');
					productImage.setAttribute("id", "img_" + i);
					productImage.setAttribute("class", "main_image");
					var img = new Image();
					img.setAttribute("data", "img_" + i);
					img.onload = function () {
						console.log("this.naturalWidth");
						console.log(this.naturalWidth);
						console.log(this.naturalHeight);
						var ratio = this.naturalWidth / this.naturalHeight;
						var newWidth, newHeight;
						if (this.naturalWidth >= this.naturalHeight) {
							newWidth = 400;
							newHeight = newWidth / ratio;
							if (newHeight > 485) {
								newHeight = 485;
								newWidth = ratio * newHeight;
							}
						}
						else {
							newHeight = 485;
							newWidth = ratio * newHeight;
							if (newWidth > 400) {
								newWidth = 400;
								newHeight = newWidth / ratio;
							}
						}
						var prodImg = jQuery("#" + this.getAttribute("data"))[0];
						console.log("prodImg");
						console.log(prodImg);
						console.log(newWidth);
						console.log(newHeight);
						console.log(this.src);
						prodImg.setAttributeNS(XLINKNS, "xlink:href", this.src);
						prodImg.setAttribute("width", newWidth);
						prodImg.setAttribute("height", newHeight);
						if (productData.multiColor != "yes" && productData.type != "simple" && Object.keys(colors).length) {
							prodImg.setAttribute("filter", "url(#colorMat)");
						} else {
							prodImg.setAttribute("filter", "");
						}
						//checking if this is first side
						var s = this.getAttribute("data").split("img_")[1];
						if (parseInt(s) == 0) {
							jQuery("#productSvg")[0].setAttribute("viewBox", "0 0 " + newWidth + " " + newHeight);
						}
					}

					if(productData.multiColor == "yes"){
						img.src = color.image[i];
					}else{
						img.src = productData.productImages[i];
					}

					jQuery("#productSvg").append(productImage);
					if (i != parseInt(currentSide)) {
						jQuery(productImage).hide();
					}
				}
				jQuery("#productSvg").append(jQuery("#svgroot"));

				if (productData.multiColor != "yes" && productData.type != "simple" && Object.keys(colors).length ) {
					changeProductColor(color.optionID, color.optionName);
				}
			}

		}

		function applyColorOnProduct(value){
			var color = productData.allColors.color[value];
			if(productData.multiColor == "yes"){
				jQuery(".main_image").remove();
				for (var i = 0; i < productData.noofSides; i++) {
					var productImage = document.createElementNS(SVGNS, 'image');
					productImage.setAttribute("id", "img_" + i);
					productImage.setAttribute("class", "main_image");
					var img = new Image();
					img.setAttribute("data", "img_" + i);
					img.onload = function () {
						console.log("this.naturalWidth");
						console.log(this.naturalWidth);
						console.log(this.naturalHeight);
						var ratio = this.naturalWidth / this.naturalHeight;
						var newWidth, newHeight;
						if (this.naturalWidth >= this.naturalHeight) {
							newWidth = 400;
							newHeight = newWidth / ratio;
							if (newHeight > 485) {
								newHeight = 485;
								newWidth = ratio * newHeight;
							}
						}
						else {
							newHeight = 485;
							newWidth = ratio * newHeight;
							if (newWidth > 400) {
								newWidth = 400;
								newHeight = newWidth / ratio;
							}
						}
						var prodImg = jQuery("#" + this.getAttribute("data"))[0];
						console.log("prodImg");
						console.log(prodImg);
						console.log(newWidth);
						console.log(newHeight);
						console.log(this.src);
						prodImg.setAttributeNS(XLINKNS, "xlink:href", this.src);
						prodImg.setAttribute("width", newWidth);
						prodImg.setAttribute("height", newHeight);
						if (productData.multiColor != "yes" && productData.type != "simple") {
							prodImg.setAttribute("filter", "url(#colorMat)");
						} else {
							prodImg.setAttribute("filter", "");
						}
						//checking if this is first side
						var s = this.getAttribute("data").split("img_")[1];
						if (parseInt(s) == 0) {
							jQuery("#productSvg")[0].setAttribute("viewBox", "0 0 " + newWidth + " " + newHeight);
						}
					}
					img.src = color.image[i];
					jQuery("#productSvg").append(productImage);
					if (i != parseInt(currentSide)) {
						jQuery(productImage).hide();
					}
				}
				jQuery("#productSvg").append(jQuery("#svgroot"));
			}else{
				changeProductColor(color.optionID,color.optionName);
			}
		}

		function changeProductColor(colorid, colorcode) {
			if (colorcode == 'undefined') colorcode = "#FFFFFF";
			var newColor_r = hexToR(colorcode);
			var newColor_g = hexToG(colorcode);
			var newColor_b = hexToB(colorcode);
			jQuery("#current_product_colorid").val(colorid);
			jQuery("#current_product_colorhash").val(colorcode);

			var matrix = getMatrix(colorcode);
			applyMatrixFilter(matrix);
			//}

			//hideLoader();
		}
		function applyMatrixFilter(matrixValue) {
			console.log("matrixValue = " + matrixValue);
			var gaussFilterElem = document.getElementById("feColorMatrix");
			gaussFilterElem.setAttribute("type", "matrix");
			gaussFilterElem.setAttribute("values", matrixValue);
		}
		function getMatrix(color) {
			color = strPad(color, 6, "0");
			color = color.substr(1, 6);
			var rclr = parseInt(color.substr(0, 2), 16);
			var gclr = parseInt(color.substr(2, 2), 16);
			var bclr = parseInt(color.substr(4, 2), 16);
			var matrix = Number(rclr / 255) + " 0 0 0 0 ";
			matrix += "0 " + Number(gclr / 255) + " 0 0 0 ";
			matrix += "0 0 " + Number(bclr / 255) + " 0 0 ";
			matrix += "0 0 0 1 0";
			/*Added By Ajay Confgiurable Product Start*/
			return matrix;
			// applyMatrixFilter(matrix);
			/*Added By Ajay Confgiurable Product End*/
		}
		function hexToR(h) {
			return parseInt((cutHex(h)).substring(0, 2), 16)
		};

		function hexToG(h) {
			return parseInt((cutHex(h)).substring(2, 4), 16)
		};

		function hexToB(h) {
			return parseInt((cutHex(h)).substring(4, 6), 16)
		};
		function cutHex(h) {
			return (h.charAt(0) == "#") ? h.substring(1, 7) : h
		};
		function getProductSizes(products_in_color, allSizesObj){
			var sizes = [];
			if(allSizesObj){
				// console.log("products_in_color");
				// console.log(products_in_color);
				// console.log("size products");

				for(var cnt = 0; cnt <  allSizesObj.options.length; cnt++){
					var products_in_size = allSizesObj.options[cnt].products;
					// console.log(products_in_size);
					var intersection = products_in_color.filter(function(el) {
						return products_in_size.indexOf(el) != -1
					});
					if(intersection.length){
						var size = new Object();
						size.maxQty = 999999999;
						size.minQty = 1;
						size.optionID = allSizesObj.options[cnt].id;
						size.optionName = allSizesObj.options[cnt].value;
						size.priceModifier = 0;
						size.productID = intersection[0];
						sizes.push(size);
					}
				}
			}
			return sizes;
		}

		function updateContentPosition() {
			//currentSide
			var bgRect = jQuery(document.getElementById("canvasBackground"));
			if (!bgRect.length) {
				var bgRect = jQuery(bgRectSvg);
				content.parentNode.appendChild(bgRect[0]);
			}

			console.log("productData");
			console.log(productData);
			productData.Area = new Object();
			productData.productImages = new Array();
			is_multicolor = productData.multiColor;
			productData.maskImages = new Array();
			productData.overlayImages = new Array();

			jQuery.each(productData.sides, function (key, side) {
				// console.log('side', side);
				// console.log('key', key);
				if(is_multicolor != "yes"){
					productData.productImages.push(side.image);
				}else{

				}

				productData.maskImages.push(side.mask);
				productData.overlayImages.push(side.overlay);

				if(productData.design && productData.design.config_area_ids){
					config_area_key = productData.design.config_area_ids[key];
				}else{
					config_area_key = side.configure_areas[0];
				}
				console.log("config_area_key");
				console.log(config_area_key);
				side.config_area_key = config_area_key;
				var area = side.sides_area[config_area_key].area;
				// console.log("side.sides_area[config_area_key].area")
				// console.log(area);
				var area_str = area.pos_x + "," + area.pos_y + "," + area.width + "," + area.height + "," + area.output_width + "," + area.output_height;
				productData.Area[areaNameAry[key]] = area_str;

				// productData.Area.frontArea
			});

			productData.allColors = new Object();
			productData.allColors.color = new Object();

			var color_id = productData.colorId;
			var allColorsObj, allSizesObj;

			jQuery.each(productData.attributes, function (key, attribute) {
				if(attribute.id == color_id){
					allColorsObj = attribute;
				}else{
					productData.sizeId = attribute.id;
					allSizesObj = attribute;
				}
			});

			if(allColorsObj){
				for(var clr=0; clr < allColorsObj.options.length; clr++){
					var colorObj = allColorsObj.options[clr];
					var color = new Object();
					color.colorName = colorObj.label;
					color.optionID = colorObj.id;

					if(is_multicolor != "yes" || !colorObj.value.length ){
						color.optionName = colorObj.value;
					}else{
						color.colorimage = colorObj.value[0].color;
						color.image = [];
						for(var cnt=0; cnt < colorObj.value.length; cnt++){
							color.image.push(colorObj.value[cnt].image);
						}
					}
					color.priceModifier = "0";
					color.sizes = getProductSizes(colorObj.products, allSizesObj);
					productData.allColors.color[colorObj.id] = color;
				}
				// console.log("colorObj");
				// console.log(colorObj);
			}
			
			var config_area_scale = 1;
			var area = productData.Area[areaNameAry[currentSide]].split(",");
			content.setAttribute("x", area[0] * config_area_scale);
			content.setAttribute("y", area[1] * config_area_scale);
			content.setAttribute("width", area[2] * config_area_scale);
			content.setAttribute("height", area[3] * config_area_scale);
			//productData.noofSides
			bgRect.attr("x", area[0] * config_area_scale);
			bgRect.attr("y", area[1] * config_area_scale);
			bgRect.attr("width", area[2] * config_area_scale);
			bgRect.attr("height", area[3] * config_area_scale);
			//adding clippath to hide objects going outof content for output
			if(!content.getAttribute("clip-path")){
				if(!content.getElementById("layer_clippath")){
					var path = bgRect[0].firstChild.cloneNode(true);
					// path.setAttribute("x", area[0] * config_area_scale);
					// path.setAttribute("y", area[1] * config_area_scale);
					path.setAttribute("width", area[2] * config_area_scale);
					path.setAttribute("height", area[3] * config_area_scale);
					path.setAttribute("fill","#000000");
					clipDef = document.createElementNS(SVGNS, "clipPath");
					clipDef.setAttribute('class', 'clippath');
					clipDef.setAttribute('id', "layer_clippath" );
					clipDef.appendChild(path);
					findDefs(content).appendChild(clipDef);
				}
				content.setAttribute("clip-path", "url(#layer_clippath)" );
			}
		}

		function strPad(input, pad_length, pad_string, pad_left) {
			// return input if length greater than padded length
			if (input.length >= pad_length)
				return input;

			// generate padding
			var paddedString = "";
			var cnt = pad_length - (input.length);
			for (var i = 0; i < cnt; i++)
				paddedString += pad_string;

			// concatonate results
			var resultStr = pad_left ? (paddedString + input) : (input + paddedString);

			// account for overflow if any
			if (resultStr.length > pad_length) {
				// chop off extra from result based on pad_type
				if (pad_left)
					resultStr = resultStr.substr((resultStr.length) - pad_length, resultStr.length);
				else
					resultStr = resultStr.substr(0, pad_length);
			}
			return resultStr;
		}

		function getWrappedPath(path, bbox, pathOffset, h, shapeType, wrapAmount) {
			for (var g = 0; g < path.length; g++) {
				for (var m = 1; m < path[g].length - 1; m += 2) {
					var t = m + 1,
						q = Math.floor(parseFloat(path[g][m] - pathOffset.x) / bbox.width * h),
						r = Math.ceil(parseFloat(path[g][t] - pathOffset.y) / bbox.height * h),
						q = getWrappedPoint(q, r, shapeType, wrapAmount, bbox.height, bbox.width, h);
					path[g][m] = q.x + 0;
					path[g][t] = q.y + 0;
				}
			}
			return path;
		};

		function getWrappedPoint(b, d, shapeType, h, k, f, g) {
			var m = { x: 0, y: 0 };
			m.x = f / (g - 1) * b;
			switch (shapeType) {
				case 1://PINCH = WEDGE
					m.x = f / (g - 1) * b;
					m.y = 0 <= h ? k / g * d * (g - h / 10 * b) / g + b / g * (k / 2) * (h / 10) : k / g * d * (g - (g - b) * (Math.abs(h) / 10)) / g + (g - b) / g * (k / 2) * (Math.abs(h) / 10);
					break;
				case 2://ARCHWAY = BRIDGE
					m.x = f / (g - 1) * b;
					m.y = -(k / 2) + k / g * d;
					0 <= h ? (m.y += k / 5 * Math.pow((-1 + g - 2 * b) / g, 2) * h * (d / g), m.y -= d / g * (k / 40) * h) : (m.y -= k / 5 * Math.pow((-1 + g - 2 * b) / g, 2) * Math.abs(h) * ((g - d) / g), m.y -= (g - d) / g * (k / 40) * h);
					m.y += k / 2;
					break;
				case 3://ARCH
					m.x = f / (g - 1) * b;
					m.y = k / (g - 1) * d;
					m.y = 0 <= h ? m.y + k / 5 * Math.pow((-1 + g - 2 * b) / g, 2) * h : m.y - k / 5 * Math.pow((-1 + g - 2 * b) / g, 2) * Math.abs(h);
					break;
				case 4://BULDGE
					m.x = f / (g - 1) * b;
					m.y = -(k / 2) + k / g * d;
					m.y = 0 <= h ? b < g / 2 ? m.y * Math.sqrt(1 - (g / 2 - 4 * b) / (g / 2) * (h / 10)) : m.y * Math.sqrt(Math.max(0, 1 - (g / 2 - 4 * (g - 1 - b)) / (g / 2) * (h / 10))) : b < g / 2 ? m.y * Math.pow(-1 + (g / 2 - 2 * b) / (g / 2) * (h / 10), 2) : m.y * Math.pow(-1 + (g / 2 - 2 * (g - 1 - b)) / (g / 2) * (h / 10), 2);
					break;
				case 5://BIRD'S EYE
					m.y = k / g * d;
					0 <= h ? (m.x = -(f / 2) + f / g * b, m.x *= (1 + (g - 2 * d) / g * (h / 10)) / 2) : (m.x = -(f / 2) + f / g * b, m.x *= (1 + (2 * d - g) / g * (Math.abs(h) / 10)) / 2);
					m.x += f / 2;
					break;
				case 6://SLOPE = CASCADE DOWN
					m.x = f / (g - 1) * b;
					m.y = 0 <= h ? k / g * d * ((g - h / 10 * b) / g) + b / g * (h / 10) * k : k / g * d * (g - (g - b) * (Math.abs(h) / 10)) / g + (g - b) / g * k * (Math.abs(h) / 10);
					break;
				case 7://CASCADE UP
					m.x = f / (g - 1) * b;
					m.y = 0 <= h ? k / g * d * (g - h / 10 * b) / g : k / g * d * (g - (g - b) * (Math.abs(h) / 10)) / g;
					break;
				case 8://WAVE
					m.x = f / (g - 1) * b, m.y = k / (g - 1) * d, m.y = 0 <= h ? m.y + k / 15 * Math.pow((-1 + g - 2 * b) / g, 3) * h : m.y - k / 15 * Math.pow((-1 + g - 2 * b) / g, 3) * Math.abs(h);
			}
			return m;
		};
		function getPathOffSet(b) {
			for (var d = 1E4, c = 1E4, h = { x: 0, y: 0 }, k = 0; k < b.length; k++) {
				for (var f = 1; f < b[k].length - 1; f += 2) {
					var g = f + 1, m = parseInt(parseFloat(b[k][f])), g = parseInt(parseFloat(b[k][g]));
					m < d && (d = m);
					g < c && (c = g);
				}
			}
			h.x = d;
			h.y = c;
			return h;
		};
//for text curve
		function textCurve(curveVal, bBox, Str, oldStr, U, oldStrBbox, fontName, fontSize, letterSpacing, fillColor, strokeColor, strokeWidth) {
			if (0 != curveVal) {
				ea = { sx: 1, sy: 1, w: bBox.width, h: bBox.height, x: bBox.x, bBox: k, yo: 0 };
				//console.log("Curve");
				//console.log(curveVal);//curveVal
				//console.log(bBox);//BBox
				//console.log(Str);//text string
				//console.log(oldStr);//text string
				//console.log(U);//boolean
				//console.log(oldStrBbox);//BBox
				//console.log(fontName);//font name
				//console.log(letterSpacing);//0
				var h = curveVal, s = bBox, e = 1, k = !1, ja = {}, ea;
				if (curveVal < 0) (k = !0, e = -1);
				curveVal = Math.abs(curveVal);
				f = "";
				var n = Str, m = Str;
				if (Str.length < oldStr.length && U == 0) (s = oldStrBbox, m = oldStr);
				var m = paper.print(0, 0, m, fontName, fontSize, "middle", letterSpacing),
					v = m.getBBox(!0),
					w = paper.print(0, 0, n, fontName, fontSize, "middle", letterSpacing),
					x = w.getBBox(!0),
					s = getRadious(s.width, s.height, curveVal),
					h = paper.path(arc([v.width / 2, v.y2 + s], s, -90 - curveVal / 2, curveVal - 90 - curveVal / 2)).attr({ stroke: "#0000ff" }),
					q = h.getTotalLength();
				ja[n + fontName + "SP" + letterSpacing] ? s = ja[n + fontName + "SP" + letterSpacing] : (s = (new TextMetrics(paper, n, fontName, fontSize, letterSpacing)).getMetrics(), ja[n + fontName + "SP" + letterSpacing] = s);
				m.remove();
				w.remove();
				m = Math.abs(v.width - x.width) / 2;
				v = q / (v.width - v.x);
				for (w = 0; w < n.length; w++) {
					if (Str[w] != " ") {
						var t = s[w].y,
							r = s[w].width,
							q = s[w].height,
							z = (m + s[w].x + r / 2) / v,
							x = paper.print(0, 0, n[w], fontName, fontSize, "middle", letterSpacing).attr({ fill: fillColor, stroke: strokeColor, "stroke-width": strokeWidth, "stroke-linecap": "round", "stroke-linejoin": "round" }),
							q = paper.rect(0, 0, r, q),
							B = h.getPointAtLength(z).x,
							J = h.getPointAtLength(z).y * e;
						q.transform("T" + (B - r / 2) + "," + (t + J) + "");
						r = q.getBBox(!1);
						B = x.getBBox(!0);
						t = r.x - B.x;
						r = r.y - B.y;
						B = h.getPointAtLength(z);
						//console.log("getPointAtLength");
						//console.log(B);
						z = h.getPointAtLength(z + 1);
						z = 180 * Math.atan2(z.y - B.y, z.x - B.x) / Math.PI;
						if (k) (z = 360 - z);
						x.transform("t" + t + "," + r + "r" + z + "...");
						z = Raphael.transformPath(x.attr("path"), x.transform());
						f += z;
						x.remove();
						q.remove();
					}
				}
				h.remove();
				//if(ea) (k = paper.path(f), h = k.getBBox(!0), n = h.width * ea.sx, h = h.height * ea.sy, ea.yo = -(ea.h / 2) + h / 2, s = bBox.height, A.prototype.setSize(n, h), Str.length < oldStr.length && !1 == U || !0 == U || A.prototype.setSize(ea.w, h / (n / ea.w)), A.prototype.setPosition(A.x, A.y + (s - A.height) / 2 * e), k.remove());
				return e = f;
			}
		}
		TextMetrics = function (b, d, c, h, k) {
			var f = [];
			this.init = function () {
				for (var g = 0; g < d.length; g++) {
					var m = b.print(0, 0, d.slice(0, g + 1), c, h, "middle", k), t = m.getBBox(!0);
					m.remove();
					var m = b.print(0, 0, d.slice(g, g + 1), c, h, "middle", k), q = m.getBBox(!0);
					m.remove();
					t.x = t.x + t.width - q.width;
					t.width = q.width;
					t.y = q.y;
					t.height = q.height;
					f.push(t);
				}
				m = f[0].x;
				for (g = 0; g < f.length; g++) {
					f[g].x -= m;
				}
			};
			this.getMetrics = function () {
				return f;
			};
			this.init();
		};
		getRadious = function (b, d, c) {
			var h = b = b / 2 - d / 2, k = b * (2 * Math.abs(c) * Math.PI / 360);
			k < 2 * (h + d / 2) && (b = 2 * (h + d / 2) / (2 * Math.abs(c) * Math.PI / 360), k = 2 * c * Math.PI / 360 * b);
			k > 2 * (h + d / 2) && (b = 2 * (h + d / 2) / (2 * Math.abs(c) * Math.PI / 360));
			return b;
		};
		arc = function (b, d, c, h) {
			b[0] = parseFloat(b[0]);
			b[1] = parseFloat(b[1]);
			angle = c;
			coords = toCoords(b, d, angle);
			for (path = "M " + coords[0] + " " + coords[1]; angle <= h;) {
				coords = toCoords(b, d, angle), path += " L " + coords[0] + " " + coords[1], angle += 1;
			}
			return path;
		};
		toCoords = function (b, d, c) {
			var h = c / 180 * Math.PI;
			c = b[0] + Math.cos(h) * d;
			b = b[1] + Math.sin(h) * d;
			return [c, b];
		};




		function updateBleed() {
			//trace("update bleed");
			jQuery(content).after(marginSVG);
			var viewbox = content.getAttribute("viewBox").split(" ");

			//var contentWidth = parseInt(content.getAttribute("width"));
			//var contentHeight = parseInt(content.getAttribute("height"));
			var contentWidth = parseInt(viewbox[2]);
			var contentHeight = parseInt(viewbox[3]) - 1;
			var fontsize = 11 * globalScale;
			var elem, bbox;
			//var size = getSize();
			//adding Bleed Margin
			elem = jQuery("#bleedMarginRect");
			var bleedMarginRect;
			var bleedMarginLabel;
			var bleedMarginLabelBg;
			var bleedHolder = jQuery('#bleedHolder');

			if (!elem.length) {
				bleedMarginRect = document.createElementNS(SVGNS, 'rect');
				bleedMarginLabel = document.createElementNS(SVGNS, 'text');
				bleedMarginLabelBg = document.createElementNS(SVGNS, 'rect');
				bleedHolder.appendChild(bleedMarginRect);
				bleedHolder.appendChild(bleedMarginLabelBg);
				bleedHolder.appendChild(bleedMarginLabel);
			} else {
				bleedMarginRect = jQuery('#bleedMarginRect')[0];
				bleedMarginLabel = jQuery('#bleedMarginLabel')[0];
				bleedMarginLabelBg = jQuery('#bleedMarginLabelBg')[0];
			}


			var x = ((Margin.bleedMargin.left > 0) ? 0 : Math.abs(Margin.bleedMargin.left)) * typeMap_[baseUnit] + 0.5;
			var y = ((Margin.bleedMargin.top > 0) ? 0 : Math.abs(Margin.bleedMargin.top)) * typeMap_[baseUnit] - 1.5;
			bleedMarginRect.setAttribute("id", "bleedMarginRect");
			bleedMarginRect.setAttribute("x", x);
			bleedMarginRect.setAttribute("y", y);
			bleedMarginRect.setAttribute("width", contentWidth - x - 0.5 - (((Margin.bleedMargin.right < 0) ? Math.abs(Margin.bleedMargin.right) : 0) * typeMap_[baseUnit]));
			bleedMarginRect.setAttribute("height", contentHeight - y - 0.5 - (((Margin.bleedMargin.bottom < 0) ? Math.abs(Margin.bleedMargin.bottom) : 0) * typeMap_[baseUnit]));
			bleedMarginRect.setAttribute("stroke", "#000000");
			bleedMarginRect.setAttribute("stroke-width", 1);
			bleedMarginRect.setAttribute("stroke-dasharray", "5,5");
			bleedMarginRect.setAttribute("opacity", 1);
			bleedMarginRect.setAttribute("fill", "none");
			bleedMarginRect.setAttribute("style", "pointer-events:none");


			bleedMarginLabel.setAttribute("id", "bleedMarginLabel");
			bleedMarginLabel.setAttribute("x", x + 3);
			bleedMarginLabel.setAttribute("y", y + fontsize);
			bleedMarginLabel.setAttribute("opacity", 1);
			bleedMarginLabel.setAttribute("fill", "#FFFFFF");
			bleedMarginLabel.setAttribute("font-size", fontsize);
			bleedMarginLabel.setAttribute("style", "pointer-events:none");



			bleedMarginLabel.textContent = langData[currentStore].bleed_margin;
			bbox = bleedMarginLabel.getBBox();

			bleedMarginLabelBg.setAttribute("id", "bleedMarginLabelBg");
			bleedMarginLabelBg.setAttribute("x", x);
			bleedMarginLabelBg.setAttribute("y", y);
			bleedMarginLabelBg.setAttribute("width", bbox.width + 5);
			bleedMarginLabelBg.setAttribute("height", bbox.height);
			bleedMarginLabelBg.setAttribute("stroke", "none");
			bleedMarginLabelBg.setAttribute("stroke-width", 1);
			bleedMarginLabelBg.setAttribute("opacity", 1);
			bleedMarginLabelBg.setAttribute("fill", "#000000");
			bleedMarginLabelBg.setAttribute("style", "pointer-events:none");


			//adding cut Margin
			elem = jQuery("#cutMarginRect");
			var cutMarginRect;
			var cutMarginLabel;
			var cutMarginLabelBg;

			if (!elem.length) {
				cutMarginRect = document.createElementNS(SVGNS, 'rect');
				cutMarginLabel = document.createElementNS(SVGNS, 'text');
				cutMarginLabelBg = document.createElementNS(SVGNS, 'rect');
				bleedHolder.appendChild(cutMarginRect);
				bleedHolder.appendChild(cutMarginLabelBg);
				bleedHolder.appendChild(cutMarginLabel);
			} else {
				cutMarginRect = jQuery('#cutMarginRect')[0];
				cutMarginLabel = jQuery('#cutMarginLabel')[0];
				cutMarginLabelBg = jQuery('#cutMarginLabelBg')[0];
			}
			x = ((Margin.bleedMargin.left > 0) ? Margin.bleedMargin.left : 0) * typeMap_[baseUnit];
			y = ((Margin.bleedMargin.top > 0) ? Margin.bleedMargin.top : 0) * typeMap_[baseUnit];
			var width = contentWidth - (Margin.horizontalBleedAbs * typeMap_[baseUnit]);
			var height = contentHeight - (Margin.verticalBleedAbs * typeMap_[baseUnit]);

			cutMarginRect.setAttribute("id", "cutMarginRect");
			cutMarginRect.setAttribute("x", x);
			cutMarginRect.setAttribute("y", y);
			cutMarginRect.setAttribute("width", width);
			cutMarginRect.setAttribute("height", height);
			cutMarginRect.setAttribute("stroke", "#FF0000");
			cutMarginRect.setAttribute("stroke-width", 1);
			cutMarginRect.setAttribute("stroke-dasharray", "5,5");
			cutMarginRect.setAttribute("opacity", 1);
			cutMarginRect.setAttribute("fill", "none");
			cutMarginRect.setAttribute("style", "pointer-events:none");

			cutMarginLabel.setAttribute("id", "cutMarginLabel");
			cutMarginLabel.setAttribute("x", x + width + 3);
			cutMarginLabel.setAttribute("y", y + fontsize);
			cutMarginLabel.setAttribute("opacity", 1);
			cutMarginLabel.setAttribute("fill", "#FFFFFF");
			cutMarginLabel.setAttribute("font-size", fontsize);
			cutMarginLabel.setAttribute("style", "pointer-events:none");


			cutMarginLabel.textContent = langData[currentStore].cut_margin;
			bbox = cutMarginLabel.getBBox();
			cutMarginLabel.setAttribute("x", cutMarginLabel.getAttribute("x") - bbox.width - 3);

			cutMarginLabelBg.setAttribute("id", "cutMarginLabelBg");
			cutMarginLabelBg.setAttribute("x", x + width - (bbox.width + 5));
			cutMarginLabelBg.setAttribute("y", y);
			cutMarginLabelBg.setAttribute("width", bbox.width + 5);
			cutMarginLabelBg.setAttribute("height", bbox.height);
			cutMarginLabelBg.setAttribute("stroke", "none");
			cutMarginLabelBg.setAttribute("stroke-width", 1);
			cutMarginLabelBg.setAttribute("opacity", 1);
			cutMarginLabelBg.setAttribute("fill", "#FF0000");
			cutMarginLabelBg.setAttribute("style", "pointer-events:none");

			//adding safe Margin
			elem = jQuery("#safeMarginRect");
			var safeMarginRect;
			var safeMarginLabel;
			var safeMarginLabelBg;
			if (!elem.length) {
				safeMarginRect = document.createElementNS(SVGNS, 'rect');
				safeMarginLabel = document.createElementNS(SVGNS, 'text');
				safeMarginLabelBg = document.createElementNS(SVGNS, 'rect');
				bleedHolder.appendChild(safeMarginRect);
				bleedHolder.appendChild(safeMarginLabelBg);
				bleedHolder.appendChild(safeMarginLabel);
			} else {
				safeMarginRect = jQuery('#safeMarginRect')[0];
				safeMarginLabel = jQuery('#safeMarginLabel')[0];
				safeMarginLabelBg = jQuery('#safeMarginLabelBg')[0];
			}
			x = x + (Margin.safeMargin.left * typeMap_[baseUnit]);
			y = y + (Margin.safeMargin.top * typeMap_[baseUnit]);

			safeMarginRect.setAttribute("id", "safeMarginRect");
			safeMarginRect.setAttribute("x", x);
			safeMarginRect.setAttribute("y", y);
			safeMarginRect.setAttribute("width", contentWidth - ((Margin.horizontalBleedAbs + Margin.horizontalSafe) * typeMap_[baseUnit]));
			safeMarginRect.setAttribute("height", contentHeight - ((Margin.verticalBleedAbs + Margin.verticalSafe) * typeMap_[baseUnit]));
			safeMarginRect.setAttribute("stroke", "#009900");
			safeMarginRect.setAttribute("stroke-width", 1);
			safeMarginRect.setAttribute("stroke-dasharray", "5,5");
			safeMarginRect.setAttribute("opacity", 1);
			safeMarginRect.setAttribute("fill", "none");
			safeMarginRect.setAttribute("style", "pointer-events:none");

			safeMarginLabel.setAttribute("id", "safeMarginLabel");
			safeMarginLabel.setAttribute("x", x + 3);
			safeMarginLabel.setAttribute("y", y - fontsize / 3 + parseInt(safeMarginRect.getAttribute("height")));
			safeMarginLabel.setAttribute("opacity", 1);
			safeMarginLabel.setAttribute("fill", "#FFFFFF");
			safeMarginLabel.setAttribute("font-size", fontsize);
			safeMarginLabel.setAttribute("style", "pointer-events:none");

			safeMarginLabel.textContent = langData[currentStore].safe_margin;
			//trace('safeMarginRect.getAttribute("height") = ' + safeMarginRect.getAttribute("height"));
			bbox = safeMarginLabel.getBBox();

			safeMarginLabelBg.setAttribute("id", "safeMarginLabelBg");
			safeMarginLabelBg.setAttribute("x", x);
			safeMarginLabelBg.setAttribute("y", y + parseInt(safeMarginRect.getAttribute("height")) - bbox.height);
			safeMarginLabelBg.setAttribute("width", bbox.width + 5);
			safeMarginLabelBg.setAttribute("height", bbox.height);
			safeMarginLabelBg.setAttribute("stroke", "none");
			safeMarginLabelBg.setAttribute("stroke-width", 1);
			safeMarginLabelBg.setAttribute("opacity", 1);
			safeMarginLabelBg.setAttribute("fill", "#009900");
			safeMarginLabelBg.setAttribute("style", "pointer-events:none");


			bleedHolder[0].setAttribute("width", contentWidth);
			bleedHolder[0].setAttribute("height", contentHeight);
			bleedHolder[0].setAttribute("viewBox", "0 0 " + contentWidth + " " + contentHeight);

			//checking if margins are zero then hide the labels
			if (Margin.verticalBleed + Margin.horizontalBleed == 0) {
				// jQuery("#bleedMarginRect").hide();
				jQuery("#bleedMarginLabel").hide();
				jQuery("#bleedMarginLabelBg").hide();
			}
			if (Margin.verticalSafe + Margin.horizontalSafe == 0) {
				// jQuery("#safeMarginRect").hide();
				jQuery("#safeMarginLabel").hide();
				jQuery("#safeMarginLabelBg").hide();
			}
			if (Margin.verticalSafe + Margin.horizontalSafe + Margin.verticalBleed + Margin.horizontalBleed == 0) {
				// jQuery("#cutMarginRect").hide();
				jQuery("#cutMarginLabel").hide();
				jQuery("#cutMarginLabelBg").hide();
			}
		}

		function setViewbox() {
			var bleedHolder = jQuery('#bleedHolder')[0];
			var contentWidth = parseInt(content.getAttribute("width"));
			var contentHeight = parseInt(content.getAttribute("height"));

			bleedHolder.setAttribute("width", contentWidth);
			bleedHolder.setAttribute("height", contentHeight);
			bleedHolder.setAttribute("viewBox", "0 0 " + contentWidth + " " + contentHeight);
		}

		function updateObjectInPage(obj,prop,val,side) {
			console.log("updateObjectInPage");
			console.log(side);
			var parser = new DOMParser();
			var doc, gTagLayer1,objInDoc;
			if(side == currentSide){
				var contentElem = document.getElementById("svgcontent");
				gTagLayer1 = findNodeWithTitleName(contentElem.childNodes, "Layer 1");
				objInDoc = jQuery(gTagLayer1).find("#"+obj.id)[0];
			}else{
				doc = parser.parseFromString(quickEditSvg[side], "image/svg+xml");
				gTagLayer1 = findNodeWithTitleName(doc.firstChild.childNodes, "Layer 1");
				objInDoc = doc.getElementById(obj.id);
			}
			
			if(prop.indexOf("xlink") >= 0){
				objInDoc.setAttributeNS(XLINKNS, prop, val);
			}else{
				objInDoc.setAttribute( prop, val);
			}
			console.log("quickEditSvg[side]");
			console.log(quickEditSvg[side] );
			if(currentSide != side){
				quickEditSvg[side] = new XMLSerializer().serializeToString(doc);
			}else{
				// updateCurrentSide = false;
				// drawSvg(side);
			}
		}

		function getSvgString() {
			var svgString = '';
			var svgContent = document.getElementById("svgcontent");
			if (svgContent) {
				svgString = new XMLSerializer().serializeToString(document.getElementById("svgcontent"))
			}

			if (updateCurrentSide) {
				//console.log('updateCurrentSide');
				quickEditSvg[currentSide] = svgString;
				console.log("quickEditSvg[currentSide]");
				console.log(quickEditSvg[currentSide]);
			}
			return svgString;
		}

		function setSvgString() {
			console.log("currentSide");
			console.log(currentSide);
			var parser = new DOMParser();
			console.log("quickEditSvg[currentSide]");
			console.log(quickEditSvg[currentSide]);
			var doc = parser.parseFromString(quickEditSvg[currentSide], "image/svg+xml");
			doc.childNodes[0].setAttribute("overflow", "hidden");
			doc.childNodes[0].id = 'svgcontent';
			var svg = new XMLSerializer().serializeToString(doc);
			if (toolType == "web2print") {
				var quickeditarea = document.getElementById("quickeditarea");
				quickeditarea.innerHTML = svg;
			} else {
				if (jQuery("#svgroot").length) {
					jQuery("#svgroot").empty();//this if for IE
					jQuery("#svgroot").append(svg);//this if for IE
					// jQuery("#svgroot")[0].innerHTML = "svg";
				} else {
					var quickeditarea = document.getElementById("quickeditarea");
					svg = '<svg style="width: 400px; height: 485px; position: absolute; top: 0px; left: 0px;" overflow="visible" y="" x="" height="500" width="661" xmlns:xlink="http://www.w3.org/1999/xlink" xlinkns="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="svgroot">' + svg + '</svg>';
					quickeditarea.innerHTML = svg;
				}
			}
			totObjects = jQuery("#svgcontent")[0].getElementsByTagName('*').length
		}

		function preloadSFFonts(response) {
			if(fontsToLoad > fontLoaded){
				console.log("Raphael.fonts");
				console.log(Raphael.fonts);
				if(Raphael.fonts && Raphael.fonts[sfFonts[fontLoaded]]){
					fontLoaded++;
					preloadSFFonts(response);
				}else{
					jQuery.ajax({
						url: fontData[sfFonts[fontLoaded]].jsFile,
						beforeSend: function () {
							// Element.show('loading-mask');
							jQuery('body').trigger('processStart');
						},
						complete: function () {
							// Element.hide('loading-mask');
							jQuery('body').trigger('processStop');
							fontLoaded++;
							preloadSFFonts(response);
						}
					});
				}
			}else{
				preloadSFImages(response.data);
			}
		}
		function setupCanvas() {
			//replace image path from svg 
			for(var sideCnt = 0; sideCnt < quickEditSvg.length; sideCnt++){
				quickEditSvg[sideCnt] = replaceImgPath(quickEditSvg[sideCnt]);
			}

			drawSvg(currentSide);
			jQuery("#quickedit-loader").remove();
			var conWidth = parseFloat(content.getAttribute("width")) / typeMap_[baseUnit];
			var conHeight = parseFloat(content.getAttribute("height")) / typeMap_[baseUnit]
			console.log(JSON.stringify([conWidth + baseUnit, conHeight + baseUnit]));
			var size = jQuery("<input type='hidden' name='size' id='size' value='" + JSON.stringify([conWidth + baseUnit, conHeight + baseUnit]) + "' />");
			addToCartForm.append(size);
			//setting up height of product-image
			var avail_height = jQuery(window).height() / 1.5;
			if(toolType == "producttool"){
				jQuery("#product-image").css("height",avail_height + "px");
			}else{
				
				var quickeditarea =  jQuery("#quickeditarea");
				quickeditarea.css("height", avail_height + "px");
				var avail_width = parseFloat(quickeditarea.width());
				var content_width = parseFloat(jQuery("#svgcontent").attr("width"));
				var content_height = parseFloat(jQuery("#svgcontent").attr("height"));
				if(avail_width > content_width){
					// if(content_width > content_height){
						var new_width =  avail_height * (content_width / content_height);
						if(new_width > avail_width){
							new_width = avail_width;
						}
						quickeditarea.css("width",new_width + "px");
						quickeditarea.css("margin-left",(avail_width - new_width)/2 + "px");
					// }else{
					// 	quickeditarea.css("width",content_width + "px");
					// 	quickeditarea.css("margin-left",(avail_width - content_width)/2 + "px");
					// }
				}else{
					var new_width = avail_height * (content_width / content_height);
					if(new_width > avail_width){
						new_width = avail_width;
					}
					quickeditarea.css("width",new_width + "px");
					quickeditarea.css("margin-left",(avail_width - new_width)/2 + "px");
				}
				if ((colorPickerType == "Printable" && productData.onlySingleColor == 1) || colorPickerType.toString().toLowerCase() == "onecolor") {
					if(colorPickerType.toString().toLowerCase() == "onecolor"){
						if(productData.singleColorHexCode.indexOf("#") == 0){
							productData.singleColorHexCode = productData.singleColorHexCode.substring(1);
						}
						hexCodeList = [productData.singleColorHexCode];
						colorlist = [{colorCode:productData.singleColorHexCode, name:""}];
					}
					convertColor();
				}
				if (colorPickerType == "Printable" || colorPickerType.toString().toLowerCase() == "onecolor") {
					multistyleText.changeDefaultcolor();
				}
				multistyleText.updateAllTextAreaToMultiStyle();
			}

			if (window.matchMedia('(max-width: 767px)').matches) {
				//for mobile

			} else {
				//for desktop
				
				//move svg with page scroll
				var top_min = jQuery(".product.media").offset().top;
				var media_height = jQuery(".product.media").height();
				// var media_height = jQuery(".product-info-main").height();
				jQuery(window).scroll(function(){
					// var bottom_max = jQuery(".product.info.detailed").offset().top;
					// var bottom_max = jQuery(".product-options-bottom").offset().top +  jQuery(".product-options-bottom").height() - top_min;
					var bottom_max = jQuery(".product-info-main").height();
					if (jQuery(window).scrollTop() > top_min && jQuery(window).scrollTop() + media_height < bottom_max)
					{
						var margintop = (jQuery(window).scrollTop()- top_min/3) + "px";
						// jQuery(".media").animate({
							// marginTop: margintop
						// }, 5);
						jQuery(".media").css("margin-top", margintop);
						jQuery(".media").css("transition", "all 0.2s ease-in-out 0.2s");
					}else{
						if(jQuery(window).scrollTop() <= top_min){
							var margintop = "0px";
							// jQuery(".media").animate({
							// 	marginTop: margintop
							// }, 5);
							jQuery(".media").css("margin-top", margintop);
							jQuery(".media").css("transition", "all 0.2s ease-in-out 0.2s");
						}
					}
				});
			}
		}

		var currentTextEvent;

		function drawSvg(side) {
			//var svg = quickEditSvg[side];
			jQuery('.jPicker').remove();
			getSvgString();
			updateCurrentSide = true;
			currentSide = side;
			var side_to_check = currentSide+1;
			if(productData.is_double_page == "1"){
				var pagestr = "";
				if (side_to_check == 1) {
					pagestr = (noOfSides * 2) + " - " + side_to_check + " / " + (noOfSides * 2);
				} else {
					pagestr = (side_to_check * 2 - 2) + " - " + (side_to_check * 2 - 1) + " / " + noOfSides * 2;
				}
				jQuery("#gotoPageTxt").val(pagestr);
			}else{
				jQuery("#gotoPageTxt").val(side_to_check + " / " + noOfSides);
			}
			setSvgString();
			content = document.getElementById("svgcontent");
			globalScale = (parseInt(content.getAttribute("width")) / jQuery("#quickeditarea").width())
			// var Objects = content.getElementsByTagName("g");
			updateRightPanel();
			
			// updatePostData(); /** Added by Ashok for quick edit smart field. Funtion define in smartFields-product-list.js */

			if (toolType == "web2print") {
				updateBleed();
				//set clippath attribute for rounded corner
				var viewbox = content.getAttribute("viewBox").split(" ");
				var contentWidth = parseInt(viewbox[2]);
				var contentHeight = parseInt(viewbox[3]);
				var rect = content.getBoundingClientRect();
				console.log("rect");
				console.log(rect);
				console.log("viewbox");
				console.log(viewbox);
				var scale = Math.min(rect.width / contentWidth, rect.height/contentHeight);
				jQuery("#canvas_svgClipPath").parent().attr("transform","scale("+scale+")");
			} else {
				updateContentPosition();
				createProductImage();
				//adding stroke-width to show design area properly on quickedit
				jQuery("#canvasBackground").children()[0].setAttribute("stroke-width","2");
			}
		}

		function updateRightPanel(){
			quickPanel.empty();
			var gTagLayer1 = findNodeWithTitleName(content.childNodes, "Layer 1");
			if(gTagLayer1){
				var Objects = gTagLayer1.childNodes;
				console.log("Objects");
				console.log(Objects);
				console.log("hexCodeList");
				console.log(hexCodeList);
				var fieldCreated = [];
				for (var i = Objects.length - 1; i >= 0; i--) {
					//removing xmlns:xml attribute for IE
	
					if(isIE()){
						if (Objects[i].getAttribute("type") == "textarea" || Objects[i].getAttribute("type") == "advance") {
							Objects[i].childNodes[0].childNodes[0].removeAttribute("xmlns:xml");
						}else if(Objects[i].tagName == "text") {
							Objects[i].removeAttribute("xmlns:xml");
						}
					}
					if (Objects[i].getAttribute("lockEdit") != "true" && Objects[i].getAttribute("lockedit") != "true") {
						if (Objects[i].getAttribute("type") == "photobox" && Objects[i].getAttribute("isvdp") != "true") {
							var sfid = Objects[i].getAttribute('sfid');
							if(sfid){
								if(fieldCreated.indexOf(sfid) >= 0){
									continue;
								}else{
									fieldCreated.push(sfid);
								}
							}
							var container = jQuery('<div class="rowHolder"></div>')
							var browseField = jQuery('<input style="display:none;" class="product-custom-option" id="browseField' + i + '" type="file">');
							browseField.on("change", updateImage);
							browseField.attr("elemid", Objects[i].id);
							browseField.attr("textareaid", browseField[0].id);
	
							var displayBrowseField = jQuery('<button class="btn product-custom-option detail-upload-file" id="displayBrowseField' + i + '" type="buttonm"><span><span><i class="fa fa-cloud-upload" aria-hidden="true"></i>&nbsp;&nbsp;</span></span></button>&nbsp;&nbsp;');
							displayBrowseField.on("click", startUpload);
							displayBrowseField.attr("elemid", Objects[i].id);
							displayBrowseField.attr("textareaid", browseField[0].id);
	
							var directionDiv = jQuery('<div class="directoinButton"></div>');
	
							// var btnImgEffect = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].adjustImage+'" value="'+langData[currentStore].adjustImage+'" class="btn action primary" ><i class="fa fa-edit"></i></a>');
							// btnImgEffect.attr("elemid", Objects[i].id);
							// btnImgEffect.attr("textareaid", browseField[0].id);
							// btnImgEffect.on("click", showImageEffect);
	
							// var btnReset = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].resetImage+'" value="'+langData[currentStore].resetImage+'" class="btn action primary" ><i class="fa fa-refresh"></i></a>');
							// btnReset.attr("elemid", Objects[i].id);
							// btnReset.attr("textareaid", browseField[0].id);
							// btnReset.on("click", resetImage);
	
							var btnMoveUp = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveUp+'" value="'+langData[currentStore].moveUp+'" class="btn action primary" ><i class="fa fa-arrow-up"></i></a>');
							btnMoveUp.attr("elemid", Objects[i].id);
							btnMoveUp.attr("textareaid", browseField[0].id);
							btnMoveUp.on("mousedown", { direction: "up" }, moveBtnClickHandler);
	
							var btnMoveDown = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveDown+'" value="'+langData[currentStore].moveDown+'" class="btn action primary" ><i class="fa fa-arrow-down"></i></a>');
							btnMoveDown.attr("elemid", Objects[i].id);
							btnMoveDown.attr("textareaid", browseField[0].id);
							btnMoveDown.on("mousedown", { direction: "down" }, moveBtnClickHandler);
	
							var btnMoveLeft = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveLeft+'" value="'+langData[currentStore].moveLeft+'" class="btn action primary" ><i class="fa fa-arrow-left"></i></a>');
							btnMoveLeft.attr("elemid", Objects[i].id);
							btnMoveLeft.attr("textareaid", browseField[0].id);
							btnMoveLeft.on("mousedown", { direction: "left" }, moveBtnClickHandler);
	
							var btnMoveRight = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveRight+'" value="'+langData[currentStore].moveRight+'" class="btn action primary" ><i class="fa fa-arrow-right"></i></a>');
							btnMoveRight.attr("elemid", Objects[i].id);
							btnMoveRight.attr("textareaid", browseField[0].id);
							btnMoveRight.on("mousedown", { direction: "right" }, moveBtnClickHandler);
	
	
							//adding font size buttons
							var fontsizeDiv = jQuery('<div class="fontsizeButton"></div>');
	
							var btnfontSizeUp = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].increaseSize+'" value="'+langData[currentStore].increaseSize+'" class="btn action primary" ><i class="fa fa-plus"></i></a>');
							btnfontSizeUp.attr("elemid", Objects[i].id);
							btnfontSizeUp.attr("textareaid", browseField[0].id);
							btnfontSizeUp.on("mousedown", { direction: "up" }, fontsizeBtnClickHandler);
	
							var btnfontSizeDown = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].decreaseSize+'" value="'+langData[currentStore].decreaseSize+'" class="btn action primary" ><i class="fa fa-minus"></i></a>');
							btnfontSizeDown.attr("elemid", Objects[i].id);
							btnfontSizeDown.attr("textareaid", browseField[0].id);
							btnfontSizeDown.on("mousedown", { direction: "down" }, fontsizeBtnClickHandler);
	
	
							if (Objects[i].getAttribute("label")) {
								var title =  Objects[i].getAttribute("label");
								if (Objects[i].getAttribute("isrequired") == "true" ) {
									title += " *";
								}
								var label = jQuery('<span class="label">' + title + '</span>');
								label.appendTo(container);
							}
	
							browseField.appendTo(container);
							displayBrowseField.appendTo(container);
							// btnImgEffect.appendTo(container);
							// btnReset.appendTo(container);
							fontsizeDiv.appendTo(container);
							btnfontSizeUp.appendTo(fontsizeDiv);
							btnfontSizeDown.appendTo(fontsizeDiv);
							//keep photobox movable for user
							// if(Objects[i].getAttribute("lockTransform") != "true" &&  Objects[i].getAttribute("locktransform") != "true"){
								directionDiv.appendTo(container);
								btnMoveUp.appendTo(directionDiv);
								btnMoveDown.appendTo(directionDiv);
								btnMoveLeft.appendTo(directionDiv);
								btnMoveRight.appendTo(directionDiv);
							// }
	
	
							container.appendTo(quickPanel);
						}else if (Objects[i].getAttribute("type") == "text") {
							//texts.push(Objects[i]);
							//console.log(Objects[i]);
							//load font
							var container = jQuery('<div class="rowHolder"></div>')
							var fontFamily = Objects[i].getAttribute('font-family');
							//console.log('fontFamily');
							//console.log(fontFamily);
	
							jQuery.ajax({
								url: fontData[fontFamily].jsFile,
								beforeSend: function () {
									setLoaderPos();
									// Element.show('loading-mask');
								},
								complete: function () {
									// Element.hide('loading-mask');
								}
							});
							var textArea = jQuery('<textarea id="quickTextArea' + i + '">');
							textArea.attr("elemid", Objects[i].id);
							textArea.attr("textareaid", textArea[0].id);
							textArea.on("input", updateText);
							textArea.on("focus", highlightText);
	
							var label_string = "";
							if (Objects[i].getAttribute("max-char")) {
								label_string = ' (' + langData[currentStore].charlimit + Objects[i].getAttribute("max-char") + ')';
								textArea.attr("maxlength",Objects[i].getAttribute("max-char"));
							}
							
							if (Objects[i].getAttribute("label")) {
								var title =  Objects[i].getAttribute("label");
								if (Objects[i].getAttribute("isrequired") == "true" ) {
									title += " *";
								}
								var label = jQuery('<span class="label">' + title + label_string + '</span>');
								label.appendTo(container);
							}
							
	
							//var btnUpdate = jQuery('<a href="javascript:void(0)" title="Update Text" value="Update Text" class="btn action primary" >Update Text</a>');
							//btnUpdate.attr("elemid",Objects[i].id);
							//btnUpdate.attr("textareaid",textArea[0].id);
							//textArea.width("80%");
							//btnUpdate.on("click",addUpdateShapeText);
							var directionDiv = jQuery('<div class="directoinButton"></div>');
	
							var btnMoveUp = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveUp+'" value="'+langData[currentStore].moveUp+'" class="btn action primary" ><i class="fa fa-arrow-up"></i></a>');
							btnMoveUp.attr("elemid", Objects[i].id);
							btnMoveUp.attr("textareaid", textArea[0].id);
							btnMoveUp.on("mousedown", { direction: "up" }, moveBtnClickHandler);
	
							var btnMoveDown = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveDown+'" value="'+langData[currentStore].moveDown+'" class="btn action primary" ><i class="fa fa-arrow-down"></i></a>');
							btnMoveDown.attr("elemid", Objects[i].id);
							btnMoveDown.attr("textareaid", textArea[0].id);
							btnMoveDown.on("mousedown", { direction: "down" }, moveBtnClickHandler);
	
							var btnMoveLeft = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveLeft+'" value="'+langData[currentStore].moveLeft+'" class="btn action primary" ><i class="fa fa-arrow-left"></i></a>');
							btnMoveLeft.attr("elemid", Objects[i].id);
							btnMoveLeft.attr("textareaid", textArea[0].id);
							btnMoveLeft.on("mousedown", { direction: "left" }, moveBtnClickHandler);
	
							var btnMoveRight = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveRight+'" value="'+langData[currentStore].moveRight+'" class="btn action primary" ><i class="fa fa-arrow-right"></i></a>');
							btnMoveRight.attr("elemid", Objects[i].id);
							btnMoveRight.attr("textareaid", textArea[0].id);
							btnMoveRight.on("mousedown", { direction: "right" }, moveBtnClickHandler);
	
	
							// var clrPicker = jQuery('<input type="color" title="change color" class="blueBtn" />');
							var clrPicker = jQuery('<span title="'+langData[currentStore].changeColor+'" class="clrPicker" >&nbsp;</span>');
	
							clrPicker.attr("elemid", Objects[i].id);
							clrPicker.attr("textareaid", textArea[0].id);
							clrPicker.attr("color", getFillColor(Objects[i]));
	
							//clrPicker.on("change",changeColor);
							//clrPicker.val(getFillColor(Objects[i]));
	
							//adding font size buttons
							var fontsizeDiv = jQuery('<div class="fontsizeButton"></div>');
	
							var btnFontFamily = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].changeFontFamily+'" value="'+langData[currentStore].changeFontFamily+'" class="btn action primary" ><i class="fa fa-font"></i></a>');
							btnFontFamily.attr("elemid", Objects[i].id);
							btnFontFamily.attr("textareaid", textArea[0].id);
							btnFontFamily.on("click", { direction: "up" }, fontfamilyBtnClickHandler);
	
							var btnfontSizeUp = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].increaseSize+'" value="'+langData[currentStore].increaseSize+'" class="btn action primary" ><i class="fa fa-plus"></i></a>');
							btnfontSizeUp.attr("elemid", Objects[i].id);
							btnfontSizeUp.attr("textareaid", textArea[0].id);
							btnfontSizeUp.on("mousedown", { direction: "up" }, fontsizeBtnClickHandler);
	
							var btnfontSizeDown = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].decreaseSize+'" value="'+langData[currentStore].decreaseSize+'" class="btn action primary" ><i class="fa fa-minus"></i></a>');
							btnfontSizeDown.attr("elemid", Objects[i].id);
							btnfontSizeDown.attr("textareaid", textArea[0].id);
							btnfontSizeDown.on("mousedown", { direction: "down" }, fontsizeBtnClickHandler);
	
							textArea.val(Objects[i].getAttribute("text").replace(/&quote;/g, '"').replace(/@##@#@/g, "\n"));
	
							container.appendTo(quickPanel);
							textArea.appendTo(container);
							clrPicker.appendTo(container);
	
							fontsizeDiv.appendTo(container);
	
	
							if (Objects[i].getAttribute("lockTransform") != "true" && Objects[i].getAttribute("locktransform") != "true") {
								btnFontFamily.appendTo(fontsizeDiv);
								btnfontSizeUp.appendTo(fontsizeDiv);
								btnfontSizeDown.appendTo(fontsizeDiv);
	
								directionDiv.appendTo(container);
								btnMoveUp.appendTo(directionDiv);
								btnMoveDown.appendTo(directionDiv);
								btnMoveLeft.appendTo(directionDiv);
								btnMoveRight.appendTo(directionDiv);
							}
	
	
	
							//btnUpdate.appendTo(quickPanel);
	
							createPicker(clrPicker);
						}else if (Objects[i].getAttribute("type") == "advance") {
							var container = jQuery('<div class="rowHolder"></div>')
							var textArea = jQuery('<textarea readonly id="quickTextArea' + i + '">');
							textArea.attr("elemid", Objects[i].id);
							textArea.attr("textareaid", textArea[0].id);
							// textArea.on("input", updateText);
							textArea.on("focus", highlightText);

							var label_string = "";	
							if (Objects[i].getAttribute("label")) {
								var title =  Objects[i].getAttribute("label");
								if (Objects[i].getAttribute("isrequired") == "true" ) {
									title += " *";
								}
								var label = jQuery('<span class="label">' + title + label_string + '</span>');
								label.appendTo(container);
							}

							// var btnEdit = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].edit+'" value="'+langData[currentStore].edit+'" class="btn action primary" ><i class="fa fa-edit"></i></a>');
							var btnEdit = jQuery('<span title="'+ langData[currentStore].edit +'" class="clrPicker" ><i class="fa fa-edit"></i></span>');
							btnEdit.attr("elemid", Objects[i].id);
							btnEdit.attr("textareaid", textArea[0].id);
							btnEdit.on("click", editAdvanceText);

							var directionDiv = jQuery('<div class="directoinButton"></div>');
	
							var btnMoveUp = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveUp+'" value="'+langData[currentStore].moveUp+'" class="btn action primary" ><i class="fa fa-arrow-up"></i></a>');
							btnMoveUp.attr("elemid", Objects[i].id);
							btnMoveUp.attr("textareaid", textArea[0].id);
							btnMoveUp.on("mousedown", { direction: "up" }, moveBtnClickHandler);
	
							var btnMoveDown = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveDown+'" value="'+langData[currentStore].moveDown+'" class="btn action primary" ><i class="fa fa-arrow-down"></i></a>');
							btnMoveDown.attr("elemid", Objects[i].id);
							btnMoveDown.attr("textareaid", textArea[0].id);
							btnMoveDown.on("mousedown", { direction: "down" }, moveBtnClickHandler);
	
							var btnMoveLeft = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveLeft+'" value="'+langData[currentStore].moveLeft+'" class="btn action primary" ><i class="fa fa-arrow-left"></i></a>');
							btnMoveLeft.attr("elemid", Objects[i].id);
							btnMoveLeft.attr("textareaid", textArea[0].id);
							btnMoveLeft.on("mousedown", { direction: "left" }, moveBtnClickHandler);
	
							var btnMoveRight = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveRight+'" value="'+langData[currentStore].moveRight+'" class="btn action primary" ><i class="fa fa-arrow-right"></i></a>');
							btnMoveRight.attr("elemid", Objects[i].id);
							btnMoveRight.attr("textareaid", textArea[0].id);
							btnMoveRight.on("mousedown", { direction: "right" }, moveBtnClickHandler);

							//adding font size buttons
							var fontsizeDiv = jQuery('<div class="fontsizeButton"></div>');
	
							var btnfontSizeUp = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].increaseSize+'" value="'+langData[currentStore].increaseSize+'" class="btn action primary" ><i class="fa fa-plus"></i></a>');
							btnfontSizeUp.attr("elemid", Objects[i].id);
							btnfontSizeUp.attr("textareaid", textArea[0].id);
							btnfontSizeUp.on("mousedown", { direction: "up" }, fontsizeBtnClickHandler);
	
							var btnfontSizeDown = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].decreaseSize+'" value="'+langData[currentStore].decreaseSize+'" class="btn action primary" ><i class="fa fa-minus"></i></a>');
							btnfontSizeDown.attr("elemid", Objects[i].id);
							btnfontSizeDown.attr("textareaid", textArea[0].id);
							btnfontSizeDown.on("mousedown", { direction: "down" }, fontsizeBtnClickHandler);
	
	
							textArea.val(getMultiTextContent(Objects[i].childNodes[0].childNodes[0]));

							container.appendTo(quickPanel);
							textArea.appendTo(container);
							btnEdit.appendTo(container);
							fontsizeDiv.appendTo(container);

							if (Objects[i].getAttribute("lockTransform") != "true" && Objects[i].getAttribute("locktransform") != "true") {
								btnfontSizeUp.appendTo(fontsizeDiv);
								btnfontSizeDown.appendTo(fontsizeDiv);

								directionDiv.appendTo(container);
								btnMoveUp.appendTo(directionDiv);
								btnMoveDown.appendTo(directionDiv);
								btnMoveLeft.appendTo(directionDiv);
								btnMoveRight.appendTo(directionDiv);
							}

						}else if (Objects[i].getAttribute("type") == "textarea") {
							//texts.push(Objects[i]);
							//console.log(Objects[i]);
							//load font
	
							var container = jQuery('<div class="rowHolder"></div>')
							var fontFamily = Objects[i].getAttribute('font-family');
							//console.log('fontFamily');
							//console.log(fontFamily);
	
							// jQuery.ajax({
							// 	url: fontDirectory + fontData[fontFamily].jsFile,
							// 	beforeSend: function () {
							// 		setLoaderPos();
							// 		Element.show('loading-mask');
							// 	},
							// 	complete: function () {
							// 		Element.hide('loading-mask');
							// 	}
							// });
							var textArea = jQuery('<textarea id="quickTextArea' + i + '">');
							textArea.attr("elemid", Objects[i].id);
							textArea.attr("textareaid", textArea[0].id);
							textArea.on("input", updateText);
							textArea.on("focus", highlightText);

							var label_string = "";
							if (Objects[i].getAttribute("max-char")) {
								label_string = ' (' + langData[currentStore].charlimit + Objects[i].getAttribute("max-char") + ')';
								textArea.attr("maxlength",Objects[i].getAttribute("max-char"));
							}
	
							if (Objects[i].getAttribute("label")) {
								var title =  Objects[i].getAttribute("label");
								if (Objects[i].getAttribute("isrequired") == "true" ) {
									title += " *";
								}
								var label = jQuery('<span class="label">' + title + label_string + '</span>');
								label.appendTo(container);
							}
	
							//var btnUpdate = jQuery('<a href="javascript:void(0)" title="Update Text" value="Update Text" class="btn action primary" >Update Text</a>');
							//btnUpdate.attr("elemid",Objects[i].id);
							//btnUpdate.attr("textareaid",textArea[0].id);
							//textArea.width("80%");
							//btnUpdate.on("click",addUpdateShapeText);
							var directionDiv = jQuery('<div class="directoinButton"></div>');
	
							var btnMoveUp = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveUp+'" value="'+langData[currentStore].moveUp+'" class="btn action primary" ><i class="fa fa-arrow-up"></i></a>');
							btnMoveUp.attr("elemid", Objects[i].id);
							btnMoveUp.attr("textareaid", textArea[0].id);
							btnMoveUp.on("mousedown", { direction: "up" }, moveBtnClickHandler);
	
							var btnMoveDown = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveDown+'" value="'+langData[currentStore].moveDown+'" class="btn action primary" ><i class="fa fa-arrow-down"></i></a>');
							btnMoveDown.attr("elemid", Objects[i].id);
							btnMoveDown.attr("textareaid", textArea[0].id);
							btnMoveDown.on("mousedown", { direction: "down" }, moveBtnClickHandler);
	
							var btnMoveLeft = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveLeft+'" value="'+langData[currentStore].moveLeft+'" class="btn action primary" ><i class="fa fa-arrow-left"></i></a>');
							btnMoveLeft.attr("elemid", Objects[i].id);
							btnMoveLeft.attr("textareaid", textArea[0].id);
							btnMoveLeft.on("mousedown", { direction: "left" }, moveBtnClickHandler);
	
							var btnMoveRight = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveRight+'" value="'+langData[currentStore].moveRight+'" class="btn action primary" ><i class="fa fa-arrow-right"></i></a>');
							btnMoveRight.attr("elemid", Objects[i].id);
							btnMoveRight.attr("textareaid", textArea[0].id);
							btnMoveRight.on("mousedown", { direction: "right" }, moveBtnClickHandler);
	
	
							//var clrPicker = jQuery('<input type="color" title="change color" class="blueBtn" />');
							var clrPicker = jQuery('<span title="'+langData[currentStore].changeColor+'" class="clrPicker" >&nbsp;</span>');
							clrPicker.attr("elemid", Objects[i].id);
							clrPicker.attr("textareaid", textArea[0].id);
							clrPicker.attr("color", Objects[i].childNodes[0].childNodes[0].getAttribute("fill"));
							//clrPicker.on("change",changeColor);
							//clrPicker.val(getFillColor(Objects[i]));
	
							//adding font size buttons
							var fontsizeDiv = jQuery('<div class="fontsizeButton"></div>');
	
							var btnFontFamily = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].changeFontFamily+'" value="'+langData[currentStore].changeFontFamily+'" class="btn action primary" ><i class="fa fa-font"></i></a>');
							btnFontFamily.attr("elemid", Objects[i].id);
							btnFontFamily.attr("textareaid", textArea[0].id);
							btnFontFamily.on("click", { direction: "up" }, fontfamilyBtnClickHandler);
	
							var btnfontSizeUp = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].increaseSize+'" value="'+langData[currentStore].increaseSize+'" class="btn action primary" ><i class="fa fa-plus"></i></a>');
							btnfontSizeUp.attr("elemid", Objects[i].id);
							btnfontSizeUp.attr("textareaid", textArea[0].id);
							btnfontSizeUp.on("mousedown", { direction: "up" }, fontsizeBtnClickHandler);
	
							var btnfontSizeDown = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].decreaseSize+'" value="'+langData[currentStore].decreaseSize+'" class="btn action primary" ><i class="fa fa-minus"></i></a>');
							btnfontSizeDown.attr("elemid", Objects[i].id);
							btnfontSizeDown.attr("textareaid", textArea[0].id);
							btnfontSizeDown.on("mousedown", { direction: "down" }, fontsizeBtnClickHandler);
	
							textArea.val(Objects[i].getAttribute("text").replace(/&quote;/g, '"').replace(/@##@#@/g, "\n"));
	
							container.appendTo(quickPanel);
							textArea.appendTo(container);
							clrPicker.appendTo(container);
	
							fontsizeDiv.appendTo(container);
	
	
							if (Objects[i].getAttribute("lockTransform") != "true" && Objects[i].getAttribute("locktransform") != "true") {
								btnFontFamily.appendTo(fontsizeDiv);
								btnfontSizeUp.appendTo(fontsizeDiv);
								btnfontSizeDown.appendTo(fontsizeDiv);
	
								directionDiv.appendTo(container);
								btnMoveUp.appendTo(directionDiv);
								btnMoveDown.appendTo(directionDiv);
								btnMoveLeft.appendTo(directionDiv);
								btnMoveRight.appendTo(directionDiv);
							}
	
	
	
							//btnUpdate.appendTo(quickPanel);
	
							createPicker(clrPicker);
	
						}else if (Objects[i].tagName == "text" && Objects[i].getAttribute("isvdp") != "true") {
							//texts.push(Objects[i]);
							//console.log(Objects[i]);
							//load font
							console.log("sfid");
							console.log(Objects[i].getAttribute('sfid'));
							var sfid = Objects[i].getAttribute('sfid');
							if(sfid){
								if(fieldCreated.indexOf(sfid) >= 0){
									continue;
								}else{
									fieldCreated.push(sfid);
								}
							}
							var container = jQuery('<div class="rowHolder"></div>')
							var fontFamily = Objects[i].getAttribute('font-family');
							//console.log('fontFamily');
							//console.log(fontFamily);
	
							// jQuery.ajax({
							// 	url: fontDirectory + fontData[fontFamily].jsFile,
							// 	beforeSend: function () {
							// 		setLoaderPos();
							// 		Element.show('loading-mask');
							// 	},
							// 	complete: function () {
							// 		Element.hide('loading-mask');
							// 	}
							// });
							var textArea = jQuery('<textarea id="quickTextArea' + i + '">');
							textArea.attr("elemid", Objects[i].id);
							textArea.attr("textareaid", textArea[0].id);
							textArea.on("input", updateText);
							textArea.on("sfid", sfid);
							textArea.on("focus", highlightText);

							var label_string = "";
							if (Objects[i].getAttribute("max-char")) {
								label_string = ' (' + langData[currentStore].charlimit + Objects[i].getAttribute("max-char") + ')';
								textArea.attr("maxlength",Objects[i].getAttribute("max-char"));
							}
	
							if (Objects[i].getAttribute("label")) {
								var title =  Objects[i].getAttribute("label");
								if (Objects[i].getAttribute("isrequired") == "true" ) {
									title += " *";
								}
								var label = jQuery('<span class="label">' + title + label_string + '</span>');
								label.appendTo(container);
							}
	
							//var btnUpdate = jQuery('<a href="javascript:void(0)" title="Update Text" value="Update Text" class="btn action primary" >Update Text</a>');
							//btnUpdate.attr("elemid",Objects[i].id);
							//btnUpdate.attr("textareaid",textArea[0].id);
							//textArea.width("80%");
							//btnUpdate.on("click",addUpdateShapeText);
							var directionDiv = jQuery('<div class="directoinButton"></div>');
	
							var btnMoveUp = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveUp+'" value="'+langData[currentStore].moveUp+'" class="btn action primary" ><i class="fa fa-arrow-up"></i></a>');
							btnMoveUp.attr("elemid", Objects[i].id);
							btnMoveUp.attr("textareaid", textArea[0].id);
							btnMoveUp.on("mousedown", { direction: "up" }, moveBtnClickHandler);
	
							var btnMoveDown = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveDown+'" value="'+langData[currentStore].moveDown+'" class="btn action primary" ><i class="fa fa-arrow-down"></i></a>');
							btnMoveDown.attr("elemid", Objects[i].id);
							btnMoveDown.attr("textareaid", textArea[0].id);
							btnMoveDown.on("mousedown", { direction: "down" }, moveBtnClickHandler);
	
							var btnMoveLeft = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveLeft+'" value="'+langData[currentStore].moveLeft+'" class="btn action primary" ><i class="fa fa-arrow-left"></i></a>');
							btnMoveLeft.attr("elemid", Objects[i].id);
							btnMoveLeft.attr("textareaid", textArea[0].id);
							btnMoveLeft.on("mousedown", { direction: "left" }, moveBtnClickHandler);
	
							var btnMoveRight = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].moveRight+'" value="'+langData[currentStore].moveRight+'" class="btn action primary" ><i class="fa fa-arrow-right"></i></a>');
							btnMoveRight.attr("elemid", Objects[i].id);
							btnMoveRight.attr("textareaid", textArea[0].id);
							btnMoveRight.on("mousedown", { direction: "right" }, moveBtnClickHandler);
	
	
							//var clrPicker = jQuery('<input type="color" title="change color" class="blueBtn" />');
							var clrPicker = jQuery('<span title="'+langData[currentStore].changeColor+'" class="clrPicker" >&nbsp;</span>');
							clrPicker.attr("elemid", Objects[i].id);
							clrPicker.attr("textareaid", textArea[0].id);
							clrPicker.attr("color", Objects[i].getAttribute("fill"));
							//clrPicker.on("change",changeColor);
							//clrPicker.val(getFillColor(Objects[i]));
	
							//adding font size buttons
							var fontsizeDiv = jQuery('<div class="fontsizeButton"></div>');
	
							var btnFontFamily = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].changeFontFamily+'" value="'+langData[currentStore].changeFontFamily+'" class="btn action primary" ><i class="fa fa-font"></i></a>');
							btnFontFamily.attr("elemid", Objects[i].id);
							btnFontFamily.attr("textareaid", textArea[0].id);
							btnFontFamily.on("click", { direction: "up" }, fontfamilyBtnClickHandler);
	
							var btnfontSizeUp = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].increaseSize+'" value="'+langData[currentStore].increaseSize+'" class="btn action primary" ><i class="fa fa-plus"></i></a>');
							btnfontSizeUp.attr("elemid", Objects[i].id);
							btnfontSizeUp.attr("textareaid", textArea[0].id);
							btnfontSizeUp.on("mousedown", { direction: "up" }, fontsizeBtnClickHandler);
	
							var btnfontSizeDown = jQuery('<a href="javascript:void(0)" title="'+langData[currentStore].decreaseSize+'" value="'+langData[currentStore].decreaseSize+'" class="btn action primary" ><i class="fa fa-minus"></i></a>');
							btnfontSizeDown.attr("elemid", Objects[i].id);
							btnfontSizeDown.attr("textareaid", textArea[0].id);
							btnfontSizeDown.on("mousedown", { direction: "down" }, fontsizeBtnClickHandler);
	
							textArea.val(Objects[i].textContent.replace(/&quote;/g, '"').replace(/@##@#@/g, "\n"));
	
							container.appendTo(quickPanel);
							textArea.appendTo(container);
							clrPicker.appendTo(container);
	
							fontsizeDiv.appendTo(container);
	
	
							if (Objects[i].getAttribute("lockTransform") != "true" && Objects[i].getAttribute("locktransform") != "true") {
								btnFontFamily.appendTo(fontsizeDiv);
								btnfontSizeUp.appendTo(fontsizeDiv);
								btnfontSizeDown.appendTo(fontsizeDiv);
	
								directionDiv.appendTo(container);
								btnMoveUp.appendTo(directionDiv);
								btnMoveDown.appendTo(directionDiv);
								btnMoveLeft.appendTo(directionDiv);
								btnMoveRight.appendTo(directionDiv);
							}
	
	
	
							//btnUpdate.appendTo(quickPanel);
	
							createPicker(clrPicker);
	
						}
					}
				}
				if (Objects.length == 0) {
					quickPanel.html("No Objects to edit.");
				}
			}
		}


		function startUpload(evt) {
			evt.preventDefault()
			var btn = evt.currentTarget;
			var fileField = jQuery("#" + btn.getAttribute("textareaid"));
			fileField.val("");
			fileField.trigger("click");
		}
		
		function updateImage(evt) {
			console.log(evt.target);
			// get form data for POSTing
			//var vFD = document.getElementById('upload_form').getFormData(); // for FF3
			var btn = evt.target;
			var file = evt.target.files[0];

			if (file) {
				console.log(file);
				var elem = jQuery("#" + evt.target.getAttribute("elemid"));
				var ext = file.name.split(".");
				var extension = ext[ext.length - 1];
				if (extension.toLowerCase() == "png" || extension.toLowerCase() == "jpg" || extension.toLowerCase() == "jpeg" || extension.toLowerCase() == "psd" || extension.toLowerCase() == "pdf" || extension.toLowerCase() == "ai" || extension.toLowerCase() == "eps") {
					jQuery('body').trigger('processStart');
					var fd = new FormData();    
					fd.append( 'attachment', file );

					jQuery.ajax({
						url: uploadfile,
						type: 'POST',
						data: fd,
						success: function(data) {
							console.log(data);
							data = JSON.parse(data);
							if(data.error){
								alert(data.error);
							}else{
								var img = new Image();
								img.onload = function () {
									
									setImageinPhotobox(elem[0], this, false);
									// imageEffect.showPanel(elem[0],btn);
									cropImage.showPanel(elem[0],btn);
								}
								img.src = data.preview_url;
							}
							jQuery('body').trigger('processStop');
						},
						error: function (response) {
							jQuery('body').trigger('processStop');
						},
						cache: false,
						contentType: false,
						processData: false
					});
					/*
					var reader = new FileReader();
					reader.onloadend = function () {
						//console.log(reader.result);
						jQuery.ajax({
							//url         :   site_url + "inc/upload_image.php?width=96&height=60&show_small=1",
							url: uploadfile,
							cache: false,
							async: true,
							data: { 'data': reader.result },
							type: 'post',
							success: function (data) {
								console.log(uploadfolder + data);
								console.log(reader);
								var img = new Image();
								img.onload = function () {
									var imageInSvg = elem[0].childNodes[0].childNodes[0];
									// var pathBbox = Raphael.pathBBox(elem[0].childNodes[1].getAttribute("d"));
									var pathBbox = {x:parseFloat(imageInSvg.getAttribute("x")),y:parseFloat(imageInSvg.getAttribute("y")),width:parseFloat(imageInSvg.getAttribute("width")), height: parseFloat(imageInSvg.getAttribute("height"))};
									console.log("imageBbox");
									console.log(pathBbox);
									console.log("pathBbox");
									
									console.log(Raphael.pathBBox(elem[0].childNodes[1].getAttribute("d")));
									var ratio = this.naturalWidth / this.naturalHeight;
									if (this.naturalHeight / this.naturalWidth > pathBbox.width / pathBbox.height) {
										imageInSvg.setAttribute("width", pathBbox.width);
										imageInSvg.setAttribute("height", pathBbox.width / ratio);
										if (pathBbox.width / ratio < pathBbox.height) {
											imageInSvg.setAttribute("height", pathBbox.height);
											imageInSvg.setAttribute("width", pathBbox.height * ratio);
										}
									} else {
										imageInSvg.setAttribute("height", pathBbox.height);
										imageInSvg.setAttribute("width", pathBbox.height * ratio);
										if (pathBbox.height * ratio < pathBbox.width) {
											imageInSvg.setAttribute("width", pathBbox.width);
											imageInSvg.setAttribute("height", pathBbox.width / ratio);
										}
									}
									imageInSvg.setAttribute("x", pathBbox.x);
									imageInSvg.setAttribute("y", pathBbox.y);
									imageInSvg.setAttributeNS(XLINKNS, "xlink:href", this.src);
									imageInSvg.setAttribute("isadminuploaded", "false");
									imageInSvg.setAttribute("isAdminUploaded", "false");
									// Element.hide('loading-mask');
									imageEffect.showPanel(elem[0],btn);
									jQuery('body').trigger('processStop');
								}
								img.src = uploadfolder + data;
							},
							error: function (response) {
								jQuery('body').trigger('processStop');
							}
						});
					}
					reader.readAsDataURL(file); //reads the data as a URL
					*/
					// Element.show('loading-mask');
				} else {
					evt.target.value = "";
					alert("Please upload valid file!!!");
				}
			}
		}

		function replaceImage(imageObj, image, applyFilter, sideCnt){
			applyFilter = (applyFilter == false) ? applyFilter : true;
			var ratio = image.naturalWidth / image.naturalHeight;
			var newWidth, newHeight;
			if ((image.naturalHeight / image.naturalWidth) < parseInt(imageObj.getAttribute("width")) / parseInt(imageObj.getAttribute("height"))){
				newWidth = parseInt(imageObj.getAttribute("width"));
				newHeight = parseInt(imageObj.getAttribute("width")) / ratio;
				if (parseInt(imageObj.getAttribute("width")) / ratio > parseInt(imageObj.getAttribute("height"))) {
					newHeight = parseInt(imageObj.getAttribute("height"));
					newWidth = parseInt(imageObj.getAttribute("height")) * ratio;
				}
			} else {
				newHeight = parseInt(imageObj.getAttribute("height"));
				newWidth = parseInt(imageObj.getAttribute("height")) * ratio;
				if (parseInt(imageObj.getAttribute("height")) * ratio > parseInt(imageObj.getAttribute("width"))) {
					newWidth = parseInt(imageObj.getAttribute("width"));
					newHeight = parseInt(imageObj.getAttribute("width")) / ratio;
				}
			}
			imageObj.setAttribute("width", newWidth);
			imageObj.setAttribute("height", newHeight);
			if(imageObj.hasAttribute("origwidth")){
				imageObj.removeAttribute("origwidth");
			}
			if(imageObj.hasAttribute("origheight")){
				imageObj.removeAttribute("origheight");
			}
			imageObj.setAttribute("origWidth", image.naturalWidth);
			imageObj.setAttribute("origHeight", image.naturalHeight);
			imageObj.setAttribute("orighref", image.src);
			// imageObj.setAttribute("x", parseInt(imageObj.getAttribute("x")) + (parseInt(imageObj.getAttribute("width")) - newWidth) / 2);
			// imageObj.setAttribute("y", parseInt(imageObj.getAttribute("y")) + (parseInt(imageObj.getAttribute("height")) - newHeight) / 2);
			imageObj.setAttributeNS(XLINKNS, "xlink:href", image.src);
			if(imageObj.hasAttribute("isadminuploaded")){
				imageObj.removeAttribute("isadminuploaded");
			}
			imageObj.setAttribute("isAdminUploaded", "false");
			if(imageObj.getAttribute("filtertype") && imageObj.getAttribute("filtertype") != "" && applyFilter){
				imageEffect.applyFilterOnImage(imageObj, image.src, image.naturalWidth, image.naturalHeight,imageObj.getAttribute("filtertype"), sideCnt);
			}
			if(imageObj.getAttribute("isrequired") == "true" ){
				imageObj.setAttribute("isrequired", "false");
			}
		}

		function setImageinPhotobox(photobox, image, applyFilter, sideCnt){
			applyFilter = (applyFilter == false) ? applyFilter : true;
			var imageInSvg = photobox.childNodes[0].childNodes[0];
			// var pathBbox = Raphael.pathBBox(photobox.childNodes[1].getAttribute("d"));
			// var pathBbox = {x:parseFloat(imageInSvg.getAttribute("x")),y:parseFloat(imageInSvg.getAttribute("y")),width:parseFloat(imageInSvg.getAttribute("width")), height: parseFloat(imageInSvg.getAttribute("height"))};
			var pathBbox = Raphael.pathBBox(photobox.childNodes[1].getAttribute("d"));
			console.log("pathBbox setImageinPhotobox");
			console.log(pathBbox);
			
			console.log(Raphael.pathBBox(photobox.childNodes[1].getAttribute("d")));
			var ratio = image.naturalWidth / image.naturalHeight;
			var newWidth, newHeight;
			if (image.naturalHeight / image.naturalWidth < pathBbox.width / pathBbox.height) {
				newWidth = pathBbox.width;
				newHeight = pathBbox.width / ratio;
				if (pathBbox.width / ratio > pathBbox.height) {
					newHeight = pathBbox.height;
					newWidth = pathBbox.height * ratio;
				}
			} else {
				newHeight = pathBbox.height;
				newWidth = pathBbox.height * ratio;
				if (pathBbox.height * ratio > pathBbox.width) {
					newWidth = pathBbox.width;
					newHeight = pathBbox.width / ratio;
				}
			}
			imageInSvg.setAttribute("width", newWidth);
			imageInSvg.setAttribute("height", newHeight);
			if(imageInSvg.hasAttribute("origwidth")){
				imageInSvg.removeAttribute("origwidth");
			}
			if(imageInSvg.hasAttribute("origheight")){
				imageInSvg.removeAttribute("origheight");
			}
			imageInSvg.setAttribute("origWidth", image.naturalWidth);
			imageInSvg.setAttribute("origHeight", image.naturalHeight);
			imageInSvg.setAttribute("orighref", image.src);
			imageInSvg.setAttribute("x", pathBbox.x + (pathBbox.width - newWidth) / 2);
			imageInSvg.setAttribute("y", pathBbox.y + (pathBbox.height - newHeight) / 2);
			imageInSvg.setAttributeNS(XLINKNS, "xlink:href", image.src);
			if(imageInSvg.hasAttribute("isadminuploaded")){
				imageInSvg.removeAttribute("isadminuploaded");
			}
			imageInSvg.setAttribute("isAdminUploaded", "false");

			if(imageInSvg.getAttribute("filtertype") && imageInSvg.getAttribute("filtertype") != "" && applyFilter){
				imageEffect.applyFilterOnImage(imageInSvg, image.src, image.naturalWidth, image.naturalHeight,imageInSvg.getAttribute("filtertype"), sideCnt);
			}
			
			if(photobox.getAttribute("isrequired") == "true" ){
				photobox.setAttribute("isrequired", "false");
			}
		}

		function closefontDiv(){
			jQuery("#font_container").hide();
		}

		function changeFontFamily(evt) {
			var li = evt.currentTarget;
			var newFontFamily = jQuery(li).find("a").attr("font-name");
			console.log("newFontFamily");
			console.log(newFontFamily);
			var btn = currentTextEvent.currentTarget;
			var elem = jQuery("#" + btn.getAttribute("elemid"));
			if(elem.attr("sfid")){
				updateSmartFieldValuesOnCurrentPage(elem.attr("sfid"),"font", newFontFamily, btn);
				jQuery("#font_container").hide();
			}else{
				elem.attr("font-family", newFontFamily);
				if(elem.attr("type") == "text" ){
					if(Raphael.fonts[newFontFamily]){
						addUpdateShapeText(currentTextEvent);
						jQuery("#font_container").hide();
					}else{
						jQuery.ajax({
							url: fontData[newFontFamily].jsFile,
							beforeSend: function () {
								jQuery('body').trigger('processStart');							
								// Element.show('loading-mask');
							},
							complete: function () {
								// Element.hide('loading-mask');
								jQuery('body').trigger('processStop');
								addUpdateShapeText(currentTextEvent);
								jQuery("#font_container").hide();
							}
						});
					}
				}else if(elem.attr("type") == "textarea" ){
					var text = elem[0].childNodes[0].childNodes[0];
					text.setAttribute("font-family", newFontFamily);
					autoWrapText(text);
					reAlignHorizontal(text,text.getAttribute("align"));
					alignVertical(elem[0]);
					jQuery("#font_container").hide();
				}else{
					jQuery("#font_container").hide();
				}
			}
		}

		function alignVertical(textElem, align){
			align = align || textElem.getAttribute('align-vertical');
			var text = textElem.childNodes[0].childNodes[0];
			var b = textElem.childNodes[1].getBBox();
			var y;
			var textBbox = text.getBBox();
			var tspanBbox;
			if(text.getElementsByTagName("tspan").length > 1){
				var cloneText = text.cloneNode(true);
				jQuery("#svgcontent")[0].appendChild(cloneText);
				while(cloneText.getElementsByTagName("tspan").length > 1){
					cloneText.removeChild(cloneText.childNodes[0]);
				}
				tspanBbox = cloneText.getBBox();
				jQuery("#svgcontent")[0].removeChild(cloneText);
			}else{
				tspanBbox = textBbox;
			}

			textElem.setAttribute('align-vertical', align);
			text.setAttribute('align-vertical', align);
			if (align == 'bottom') {
				y = b.y + b.height - textBbox.height + tspanBbox.height/2;
			} else if (align == 'top') {
				y = b.y + tspanBbox.height/2;
			} else if (align == 'middle') {
				y = b.y + b.height/2 - textBbox.height/2 + tspanBbox.height/2;
			}
			console.log("y");
			console.log(y);
			text.setAttribute('y', y);
			reAlignTextVertically(text);
			// updateArea(textElem);
		}

		function reAlignTextVertically(text){
			var lineHeight = text.getAttribute("lineHeight") ? text.getAttribute("lineHeight") : 1;
			var spans = jQuery(text).find('tspan');
			//trace("spans.length = "  + spans.length);

			// var rot = svgCanvas.getRotationAngle(text);
			// if(rot){
			// 	svgCanvas.setRotationAngle(0,true,text);
			// }


			if(spans.length > 0){
				jQuery.each(spans, function(k, span) {
					//if(k > 0)
					span.setAttribute("y", parseFloat(text.getAttribute("y")));
				});
				var bbox = text.getBBox();
				jQuery.each(spans, function(k, span) {
					//if(k > 0)
					span.setAttribute("y", parseFloat(text.getAttribute('y'))+(bbox.height )*k*lineHeight);
					// inkscape and IE does not understand dominant-baseline": "mathematical",
					// so we have kept that calculation in dy
					span.setAttribute("dy", parseFloat(text.getAttribute('font-size'))*0.37);
				});
				//runExtensions("elementChanged", {
				//elems: [text]
				//});
			}

			// if(rot){
			// 	svgCanvas.setRotationAngle(rot,true,text);
			// }
		}

		function reAlignHorizontal(elm, align, x) {
			if (!align) {
				align = 'start';
			}
			if (!x) {
				// elm.setAttribute('x',elm.parentNode.parentNode.childNodes[1].getAttribute("x"));
				x = elm.getAttribute('x');
			}else{
				elm.setAttribute('x',x);
			}
			var bbox;
			var spans = jQuery(elm).find('tspan');
			// if (spans.length > 1) {
			jQuery.each(spans, function (index, span) {
				span.setAttribute('x', x);
			});
			// }
			//svgCanvas.runExtensions("elementChanged", {
			//elems: [elm]
			//});
		}

		function fontfamilyBtnClickHandler(evt) {
			currentTextEvent = evt;
			jQuery("#font_container").show();
			jQuery("#font_container").css("top", "0px");
			jQuery("#font_container").css("left", "0px");

			var btn = currentTextEvent.currentTarget;
			var elem = jQuery("#" + btn.getAttribute("elemid"));

			jQuery("#font_container li").removeClass("active");
			jQuery("#font_container li[font-name='"+elem.attr("font-family")+"']").addClass("active");

			var btnPos = btn.getBoundingClientRect();
			var fontConPos = jQuery("#font_container")[0].getBoundingClientRect();

			var topPos = (parseFloat(btnPos.height) + parseFloat(btnPos.top) - parseFloat(fontConPos.top));
			var leftPos = (parseFloat(btnPos.left) - parseFloat(fontConPos.left) - parseFloat(fontConPos.width)/2);
			if(leftPos < 10){
				leftPos = 10;
			}
			jQuery("#font_container").css("top", topPos + "px");
			jQuery("#font_container").css("left", leftPos + "px");
		}
		var sizeInterval;
		function fontsizeBtnClickHandler(evt) {
			sizeObjectOnEnterframe(evt);
			sizeInterval = setInterval(sizeObjectOnEnterframe,100,evt);
			document.addEventListener("mouseup", clearsizeInterval);
		}

		function clearsizeInterval(evt) {
			clearInterval(sizeInterval);
		}

		function sizeObjectOnEnterframe(evt) {
			var btn = evt.currentTarget;
			var elem = jQuery("#" + btn.getAttribute("elemid"));
			if(elem.attr("sfid")){
				updateSmartFieldValuesOnCurrentPage(elem.attr("sfid"), "size", evt.data.direction, btn  )
			}else{
				resizeSelectedObject(elem, evt.data.direction, evt);
				//console.log("textarea");
				//console.log(textarea);
				if (btn.getAttribute("textareaid")) {
					var textarea = jQuery("#" + btn.getAttribute("textareaid"));
					checkOutOfBound(textarea, elem[0]);
				}
			}
		}
		//evt is for shape text
		function resizeSelectedObject(elem, direction, evt){
			if (elem.attr("type") == "photobox") {
				var imageInSvg = elem[0].childNodes[0].childNodes[0];
				var newWidth = parseFloat(imageInSvg.getAttribute("width"));
				var ratio = parseFloat(imageInSvg.getAttribute("width")) / parseFloat(imageInSvg.getAttribute("height"));
				var diff = Math.pow(10, Math.round(newWidth).toString().length - 1);
				if (diff <= 1) {
					diff = 1;
				} else {
					diff = Math.round(newWidth) / diff;
				}
				var dx = dy = 0;
				if (direction == "up") {
					newWidth += diff;
					dx = -1 * diff / 2;
					dy = -1 * (diff / ratio) / 2;
				} else {
					newWidth -= diff;
					if (newWidth < 5) {
						newWidth = 5; diff = 0;
					}
					dx = diff / 2;
					dy = (diff / ratio) / 2;
				}
				imageInSvg.setAttribute("x", parseFloat(imageInSvg.getAttribute("x")) + dx);
				imageInSvg.setAttribute("y", parseFloat(imageInSvg.getAttribute("y")) + dy);
				var scale = newWidth / parseFloat(imageInSvg.getAttribute("width"));
				imageInSvg.setAttribute("width", newWidth);
				imageInSvg.setAttribute("height", newWidth / ratio);
				var path = elem.find("path")[0];
				var paperElem = paper.path(path.getAttribute("d"));
				var newPath = Raphael.transformPath(paperElem.attr('path'), 's' + scale + ',' + scale);
				path.setAttribute("d", newPath);
				var clipPathId = elem[0].childNodes[0].getAttribute("clip-path");
				clipPathId = clipPathId.replace(/"/g, ''); // for ie
				clipPathId = clipPathId.substring(5, clipPathId.indexOf(")")); //url(#filterid)
				var clipPath = document.getElementById(clipPathId);
				//console.log(clipPath.firstChild);
				clipPath.replaceChild(path.cloneNode(true), clipPath.firstChild);
				if(isIE()){
					elem[0].childNodes[0].removeAttribute("clip-path");
					setTimeout(updateClippathdelay, 0,elem[0].childNodes[0], clipPath);
				}
			} else if (elem.attr("type") == "text") {
				var newfontsize = parseFloat(elem.attr("font-size"));
				var diff = Math.pow(10, Math.round(newfontsize).toString().length - 1);
				if (diff <= 1) {
					diff = 1;
				} else {
					diff = Math.round(newfontsize) / diff;
				}
				if (direction == "up") {
					newfontsize += diff;
				} else {
					newfontsize -= diff;
				}
				if (newfontsize < 5) newfontsize = 5;
				elem.attr("font-size", newfontsize);
				addUpdateShapeText(evt);
			}else if (elem.attr("type") == "textarea") {
				var newfontsize = parseFloat(elem.attr("font-size"));
				if (direction == "up") {
					newfontsize++;
				} else {
					newfontsize--;
				}
				elem.attr("font-size", newfontsize);
				var text = elem[0].childNodes[0].childNodes[0];
				text.setAttribute("font-size", newfontsize);
				autoWrapText(text);
				reAlignHorizontal(text,text.getAttribute("align"));
				alignVertical(elem[0]);
			}else if (elem.attr("type") == "advance") {
				var rect = elem[0].childNodes[1];
				var rectWidth = parseFloat(rect.getAttribute("width"));
				var rectHeight = parseFloat(rect.getAttribute("height"));
				var ratio = rectWidth / rectHeight;
				if (direction == "up") {
					rectWidth += 10;
				} else {
					rectWidth -= 10;
				}
				rectHeight = rectWidth / ratio;
				rect.setAttribute("width", rectWidth);
				rect.setAttribute("height", rectHeight);
				multistyleText.setTextInArea(elem[0]);
			}else if (elem[0].tagName == "text") {
				var newfontsize = parseFloat(elem.attr("font-size"));
				if (direction == "up") {
					newfontsize++;
				} else {
					newfontsize--;
				}
				elem.attr("font-size", newfontsize);
			}
		}

		function resetImage(evt) {
			var btn = evt.currentTarget;
			var textarea = jQuery("#" + btn.getAttribute("textareaid"));
			textarea[0].value = "";
			var elem = jQuery("#" + btn.getAttribute("elemid"));
			var img = elem[0].childNodes[0].childNodes[0];
			if(img.hasAttribute("isadminuploaded")){
				img.removeAttribute("isadminuploaded");
			}
			img.setAttribute("isAdminUploaded", "true");
			// img.removeAttribute("filter");
			img.setAttributeNS(XLINKNS, "xlink:href", (img.getAttribute("templatesrc") || img.getAttribute("templateSrc")));
		}

		function showImageEffect(evt) {
			var btn = evt.currentTarget;
			var elem = jQuery("#" + btn.getAttribute("elemid"));
			// imageEffect.showPanel(elem[0],btn);
			cropImage.showPanel(elem[0],btn);
		}

		var moveInterval;
		function moveBtnClickHandler(evt) {
			moveObjectOnEnterframe(evt);
			moveInterval = setInterval(moveObjectOnEnterframe,100,evt);
			document.addEventListener("mouseup", clearMoveInterval);
		}

		function clearMoveInterval(){
			clearInterval(moveInterval);
		}

		function moveObjectOnEnterframe(evt){
			var btn = evt.currentTarget;
			var elem = jQuery("#" + btn.getAttribute("elemid"));
			if(elem.attr("sfid")){
				updateSmartFieldValuesOnCurrentPage(elem.attr("sfid"), "move",evt.data.direction, btn);
			}else{
				if (elem.attr("type") == "photobox") {
					//moveSelectedObject(jQuery(elem[0].childNodes[0].childNodes[0]),evt.data.direction);
					moveSelectedObject(elem, evt.data.direction);
				} else {
					moveSelectedObject(elem, evt.data.direction);
				}
				if (btn.getAttribute("textareaid")) {
					var textarea = jQuery("#" + btn.getAttribute("textareaid"));
					checkOutOfBound(textarea, elem[0]);
				}
			}
		}



		function moveSelectedObject(elem, direction, dx, dy) {
			if( typeof dx == "undefined") dx = 0;
			if( typeof dy == "undefined") dy = 0;
			if(direction){
				switch (direction) {
					case "up":
						dx = 0;
						dy = -1 * globalScale;
						break;
					case "down":
						dx = 0;
						dy = globalScale;
						break;
					case "left":
						dx = -1 * globalScale;
						dy = 0;
						break;
					case "right":
						dx = globalScale;
						dy = 0;
						break;
				}
			}

			//if(elem[0].tagName == "image"){
			if (elem.attr("type") == "photobox") {
				//moving image
				var img = elem[0].childNodes[0].childNodes[0];
				img.setAttribute("x", parseFloat(img.getAttribute("x")) + dx);
				img.setAttribute("y", parseFloat(img.getAttribute("y")) + dy);
				//moving path
				/*
				var path = elem.find("path")[0];
				var paperElem = paper.path(path.getAttribute("d"));
				var newPath = Raphael.transformPath(paperElem.attr('path'), 't' + dx + ',' + dy);
				path.setAttribute("d", newPath);
				var clipPathId = elem[0].childNodes[0].getAttribute("clip-path");
				clipPathId = clipPathId.replace(/"/g, ''); // for ie
				clipPathId = clipPathId.substring(5, clipPathId.indexOf(")")); //url(#filterid)
				var clipPath = document.getElementById(clipPathId);
				//console.log(clipPath.firstChild);
				clipPath.replaceChild(path.cloneNode(true), clipPath.firstChild);
				if(isIE()){
					elem[0].childNodes[0].removeAttribute("clip-path");
					setTimeout(updateClippathdelay, 0,elem[0].childNodes[0], clipPath);
				}
				*/
			} else if (elem.attr("type") == "text") {
				var inc = 0;
				var paths = elem.find("path");
				while (inc < paths.length) {
					paper.clear();
					var paperElem = paper.path(paths[inc].getAttribute("d"));
					var newPath = Raphael.transformPath(paperElem.attr('path'), 't' + dx + ',' + dy);
					paths[inc].setAttribute("d", newPath);
					inc++;
				}
			} else if (elem.attr("type") == "textarea") {
				var text = elem[0].childNodes[0].childNodes[0];
				text.setAttribute("x", parseFloat(text.getAttribute("x")) + dx);
				text.setAttribute("y", parseFloat(text.getAttribute("y")) + dy);

				var rect = elem.find("rect")[0];
				rect.setAttribute("x", parseFloat(rect.getAttribute("x")) + dx);
				rect.setAttribute("y", parseFloat(rect.getAttribute("y")) + dy);

				var clipPathId = elem[0].childNodes[0].getAttribute("clip-path");
				clipPathId = clipPathId.replace(/"/g, ''); // for ie
				clipPathId = clipPathId.substring(5, clipPathId.indexOf(")")); //url(#filterid)
				var clipPath = document.getElementById(clipPathId);
				//console.log(clipPath.firstChild);
				clipPath.replaceChild(rect.cloneNode(true), clipPath.firstChild);
				if(isIE()){
					elem[0].childNodes[0].removeAttribute("clip-path");
					setTimeout(updateClippathdelay, 0,elem[0].childNodes[0], clipPath);
				}
				reAlignHorizontal(text,text.getAttribute("align"));
				alignVertical(elem[0]);
			} else if (elem.attr("type") == "advance") {
				var text = elem[0].childNodes[0].childNodes[0];
				text.setAttribute("x", parseFloat(text.getAttribute("x")) + dx);
				text.setAttribute("y", parseFloat(text.getAttribute("y")) + dy);

				var rect = elem.find("rect")[0];
				rect.setAttribute("x", parseFloat(rect.getAttribute("x")) + dx);
				rect.setAttribute("y", parseFloat(rect.getAttribute("y")) + dy);

				var clipPathId = elem[0].childNodes[0].getAttribute("clip-path");
				clipPathId = clipPathId.replace(/"/g, ''); // for ie
				clipPathId = clipPathId.substring(5, clipPathId.indexOf(")")); //url(#filterid)
				var clipPath = document.getElementById(clipPathId);
				//console.log(clipPath.firstChild);
				clipPath.replaceChild(rect.cloneNode(true), clipPath.firstChild);
				if(isIE()){
					elem[0].childNodes[0].removeAttribute("clip-path");
					setTimeout(updateClippathdelay, 0,elem[0].childNodes[0], clipPath);
				}
				multistyleText.setBxBy(elem[0]);
			} else if (elem[0].tagName == "text") {
				var text = elem[0];
				text.setAttribute("x", parseFloat(text.getAttribute("x")) + dx);
				text.setAttribute("y", parseFloat(text.getAttribute("y")) + dy);
			}
		}

		function updateClippathdelay(elem,clipPath){
			elem.setAttribute("clip-path", 'url("#' + clipPath.id + '")');
		}


		function unitInitialize() {
			// Get correct em/ex values by creating a temporary SVG.
			var svg = document.createElementNS(SVGNS, 'svg');
			document.body.appendChild(svg);
			var rect = document.createElementNS(SVGNS, 'rect');
			rect.setAttribute('width', "1em");
			rect.setAttribute('height', "1ex");
			rect.setAttribute('x', "1in");
			svg.appendChild(rect);
			var bb = rect.getBBox();
			document.body.removeChild(svg);
			var web2inkscape = 1;//0.9375;
			var inch = bb.x;
			typeMap_['em'] = bb.width * web2inkscape;
			typeMap_['ex'] = bb.height * web2inkscape;
			typeMap_['ft'] = inch * web2inkscape;
			typeMap_['in'] = inch * web2inkscape;
			typeMap_['m'] = inch * web2inkscape / 2.54;
			typeMap_['cm'] = inch * web2inkscape / 2.54;
			typeMap_['mm'] = inch * web2inkscape / 25.4;
			typeMap_['pt'] = inch * web2inkscape / 72;
			typeMap_['px'] = inch * web2inkscape / 96;
			typeMap_['pc'] = inch * web2inkscape / 6;
			typeMap_['%'] = 0;
		}


		function getNextId(){
			while(document.getElementById("svg_"+totObjects)){
				totObjects++;
			}
			return "svg_"+totObjects;
		}

		function findNodeWithTitleName(nodes, titleName) {
			console.log(nodes.length);
			for (var nt = 0; nt < nodes.length; nt++) {
				if (nodes[nt].nodeType == 1) {
					var childNode = nodes[nt].childNodes;
					for (var cnt = 0; cnt < childNode.length; cnt++) {
						if (childNode[cnt].nodeType == 1 && childNode[cnt].tagName == "title" && childNode[cnt].childNodes[0].nodeValue == titleName) {
							console.log("childNode[cnt].tagName = " + childNode[cnt].childNodes[0].nodeValue);
							return nodes[nt];
						}
					}

				}
			}
			return null;
		}

		var findDefs = function (doc) {
			var defs = doc.getElementsByTagNameNS(SVGNS, "defs");
			if (defs.length > 0) {
				defs = defs[0];
			} else {
				defs = document.createElementNS(SVGNS, "defs");
				//console.log("doc.firstChild");
				//console.log(doc);
				//console.log(doc.firstChild);

				if (doc.firstChild) {
					var gTagLayer1 = findNodeWithTitleName(doc.firstChild.childNodes, "Layer 1");
					// first child is a comment, so call nextSibling
					if (gTagLayer1) {
						gTagLayer1.parentNode.appendChild(defs);
					} else if (doc.firstChild.tagName == "svg") {
						doc.insertBefore(defs, doc.firstChild.firstChild.nextSibling);
					} else {
						doc.insertBefore(defs, doc.firstChild.nextSibling);
					}
				} else {
					doc.appendChild(defs);
				}
			}
			return defs;
		};

		var cropImage = (function () {
			var $image, updateBtn, cancelBtn, cropperObj, photobox, removeWhiteChkbox;
			var init = function () {

				var cropWindow = '<div id="cropImage_window" style="display:none;">'+
					'<div class="global_overlay"></div>'+
					'<div class="global_popup_box">'+
					'<div class="toolbar_button" >'+
					// '<button type="button" class="close-window-positoin">x</button>'+
					'</div>'+
					'<p class="new-heading pop-heading-line" id="cropimagePopupCaption">'+ langData[currentStore].adjustImage +'</p>'+
					'<div>'+
					'<div>'+
					'<div class="img-container" ><img id="crop_image" src="" /></div>'+
					'<div class="filters-button">'+
					//   '<div class="tabCon"> <span id="tabShapes">Mask</span> <span id="tabFilters">Effects</span> <span id="tabColors">Custom Effects</span> '+'</div>'+
					'<div class="clearer">&nbsp;</div>'+
					'</div>'+
					'<div class="buttonHolder"> </div>'+
					'</div>'+
					'</div>'+
					'</div>'+
					'</div>';
				var quickeditarea = document.getElementById("quickeditarea");
				if(quickeditarea != null){
					quickeditarea.parentNode.appendChild(jQuery(cropWindow)[0]);
				}
				
				removeWhiteChkbox = jQuery('<input id="removeWhiteChkbox" type="checkbox" class="rmv-checkbox"><label for="removeWhiteChkbox" class="rmv-white-label">'+langData[currentStore].removewhitelabel+'</label></input>');
				jQuery("#cropImage_window .buttonHolder").append(removeWhiteChkbox);
				removeWhiteChkbox.bind("click", removeWhiteHandler);
				
				// jQuery("#cropImage_window .close-window-positoin").bind("click", cancelBtnClickHandler);
				updateBtn = jQuery('<button type="button" style="" class="prcadcart button"><span>'+langData[currentStore].adjust+'</span></button>');
				// cancelBtn = jQuery('<button type="button" style="" class="prcadcart button"><span>'+langData[currentStore].dontAdjust+'</span></button>');
				jQuery("#cropImage_window .buttonHolder").append(updateBtn);
				jQuery("#cropImage_window .buttonHolder").append(cancelBtn);
				updateBtn.bind("click", updateBtnClickHandler);
				// cancelBtn.bind("click", cancelBtnClickHandler);

				$image = jQuery('#crop_image');

				if(isTouch()){
					jQuery("#cropImage_window .global_popup_box").css("top","30%");
					// jQuery(".global_popup_box").css("max-width","none");
					//sourceSvg.css("height", (jQuery(window).height() - 270) + "px");
					jQuery("#cropImage_window .global_popup_box").css("position","absolute");
				}else if(isIE()){
					jQuery("#cropImage_window .global_popup_box").css("top","10%");
					jQuery("#cropImage_window .global_popup_box").css("width","50%");
					jQuery("#cropImage_window .global_popup_box").css("position","absolute");
				}else{
					jQuery("#cropImage_window .global_popup_box").css("position","fixed");
					jQuery("#cropImage_window .global_popup_box").css("top","50%");
					jQuery("#cropImage_window .global_popup_box").css("transform","translateY(-50%)");
				}

				
			}

			function removeWhiteHandler(evt) {
				console.log("evt.target");
				console.log(evt.target);
				console.log(evt.currentTarget);
				if(evt.target.checked){
					jQuery('body').trigger('processStart');
					jQuery.ajax({
						url: removeWhiteUrl,
						type: 'POST',
						data: {"isQuickEdit":isQuickEdit, "imageUrl":$image.attr("src")},
						success: function (data) {
							jQuery('body').trigger('processStop');
							console.log(data);
							if(data && data.success == true){
								$image.src = data.url;
								$image.cropper('replace', data.url);
							}else{
								alert(data.success);
							}
						},
						error: function () {
						console.log('Upload error');
						}
					});
				}else{
					var src = getHref(photobox.childNodes[0].childNodes[0]);
					console.log(src);
					$image.src = src;
					$image.cropper('replace', src);
				}
			}

			function updateBtnClickHandler() {
				cropUserImage();
			}

			function cancelBtnClickHandler() {
				jQuery("#cropImage_window").hide();
			}

			function showPanel(selectedElem, $btnClicked) {
				var src;
				var aspectRatio = 16/9;;
				photobox = selectedElem;
				removeWhiteChkbox[0].checked = false;
				if (selectedElem.tagName == "g") {
					//move selected element to topleft of the canvas to get the path near to top left
					var image = selectedElem.childNodes[0].childNodes[0];
					var path = selectedElem.childNodes[1];
					var bbox = Raphael.pathBBox(path.getAttribute("d"));
					aspectRatio = bbox.width / bbox.height;
					src = getHref(image);
				} else {
					//removing clippath attribute
					src = getHref(selectedElem);
				}
				$image.attr("src", src);
				console.log("src = " + src);
				console.log("aspectRatio = " + aspectRatio);
				if(cropperObj){
					$image.cropper('replace', src);
				}else{
					$image.cropper({
						// aspectRatio: aspectRatio,
						viewMode: 2,
						zoomable: false,
						crop: function(event) {
							// console.log("event.detail.x");
							// console.log(event.detail.x);
							// console.log(event.detail.y);
							// console.log(event.detail.width);
							// console.log(event.detail.height);
							// console.log(event.detail.rotate);
							// console.log(event.detail.scaleX);
							// console.log(event.detail.scaleY);
							// console.log(cropper.getCropBoxData());
							// console.log(cropper.getCroppedCanvas());
						}
					});
		
				}
				// Get the Cropper.js instance after initialized
				cropperObj = $image.data('cropper');

				jQuery("#cropImage_window").show();
			}

			function cropUserImage(){

				cropperObj.getCroppedCanvas().toBlob(function (blob) {
					var formData = new FormData();
					var random_string = new Date().valueOf();
					var file = new File([blob], "uploaded_file_"+random_string+".png");
					formData.append('attachment', file);
					jQuery('body').trigger('processStart');
					// Use `jQuery.ajax` method
					jQuery.ajax({
						url: uploadfile,
						type: 'POST',
						data: formData,
						processData: false,
						contentType: false,
						success: function (data) {
							console.log(data);
							data = JSON.parse(data);
							console.log(data);
							var img = new Image();
							img.onload = function () {
								
								// imageEffect.showPanel(elem[0],btn);
								cancelBtnClickHandler();
								if(photobox.hasAttribute("sfid")){
									updateSmartFieldValue(photobox.getAttribute("sfid"), img);
								}else{
									setImageinPhotobox(photobox, this);
								}
								jQuery('body').trigger('processStop');
							}
							img.src = data.preview_url;
						},
						error: function () {
							console.log('Upload error');
							jQuery('body').trigger('processStop');
						}
					});
				});
			}

			return {
				name: "cropImage",
				init: init,
				showPanel: function (elem, btn) {
					showPanel(elem,btn);
				},
			}
		})();

		var imageEffect = (function () {

			var chromeMouseDownFlag = false;//chrome opens the browser dialog twice
			var shape_lib = {
				'circle': 'M140,70c0,38.659-31.34,70-70,70C31.34,140,0,108.66,0,70C0,31.34,31.34,0,70,0C108.66,0,140,31.34,140,70z',
				'rect': 'M0,0L140,0L140,140L0,140z',
				'heart': 'M70.33952,35.80746C99.34181,-52.10137 212.96642,35.80746 70.33952,148.8372C-72.28889,35.80746 41.33672,-52.10086 70.33952,35.80746z',
				'roundedSquare': 'M0.00105,14.08789C3.67214,51.49151 3.67214,89.19104 0.00105,126.59348C0.00105,134.33238 6.25158,140.68195 14.08894,140.68195C51.49193,137.01144 89.19145,137.01144 126.59508,140.68195C134.33279,140.68195 140.68236,134.43201 140.68236,126.59348C137.01068,89.19104 137.01068,51.4915 140.68236,14.08789C140.68236,6.34899 134.43242,0.00001 126.59508,0.00001L126.59508,0.09904C89.19145,3.76955 51.49193,3.76955 14.08894,0.09904C6.35062,0.09904 0.00105,6.34958 0.00105,14.18692',
				'cloudMirror': 'M43.4833984375,12.406707763671875 C42.192718505859375,13.896171569824219 40.207183837890625,14.591041564941406 38.32012939453125,14.29315185546875 C28.0938720703125,12.704597473144531 18.16552734375,18.959739685058594 14.988372802734375,28.888107299804688 C13.99560546875,31.96612548828125 13.79681396484375,35.242340087890625 14.29290771484375,38.41947937011719 C14.590789794921875,40.40562438964844 13.895904541015625,42.29205322265625 12.406463623046875,43.582733154296875 C-1.3939208984375,55.29844665527344 -4.0743408203125,75.65123748779297 6.35009765625,90.54352569580078 L6.2515869140625,90.54352569580078 C7.93927001953125,93.02521514892578 10.024505615234375,95.30921173095703 12.307952880859375,97.19623565673828 L12.40704345703125,97.19623565673828 C13.896514892578125,98.48632049560547 14.591400146484375,100.47249603271484 14.29351806640625,102.35950469970703 C12.704925537109375,112.68433380126953 18.86041259765625,122.6132583618164 28.78875732421875,125.69068145751953 C31.866790771484375,126.6834487915039 35.143585205078125,126.88162994384766 38.32073974609375,126.38617706298828 L38.32073974609375,126.48639678955078 C40.3062744140625,126.18790435791016 42.193328857421875,126.8833999633789 43.48394775390625,128.37348175048828 C55.19972229003906,142.1731948852539 75.55313110351562,144.85370635986328 90.44537353515625,134.42920684814453 C92.9263916015625,132.7415542602539 95.21112060546875,130.6557388305664 97.0975341796875,128.37348175048828 L97.0975341796875,128.2737808227539 C98.38824462890625,126.78491973876953 100.3743896484375,126.0882339477539 102.26080322265625,126.38617706298828 C112.58621215820312,127.97525787353516 122.51519775390625,121.81983184814453 125.59323120117188,111.89090728759766 C126.58535766601562,108.81224822998047 126.78408813476562,105.53667449951172 126.28924560546875,102.35948944091797 L126.3876953125,102.35948944091797 C126.08984375,100.37340545654297 126.78594970703125,98.48690032958984 128.27474975585938,97.19623565673828 C142.07510375976562,85.4804916381836 144.756103515625,65.1270980834961 134.33053588867188,50.23486328125 C132.64349365234375,47.75323486328125 130.55767822265625,45.46916198730469 128.27474975585938,43.582733154296875 C126.78594970703125,42.29205322265625 126.08984375,40.305908203125 126.3876953125,38.41947937011719 C127.97628784179688,28.094696044921875 121.82144165039062,18.16516876220703 111.89254760742188,15.087142944335938 L111.79275512695312,15.087142944335938 C108.71536254882812,14.094955444335938 105.43853759765625,13.896171569824219 102.26080322265625,14.392257690429688 L102.26080322265625,14.29315185546875 C100.274658203125,14.591041564941406 98.38824462890625,13.896171569824219 97.0975341796875,12.406707763671875 C85.3812255859375,-1.3936691284179688 65.02839660644531,-4.074699401855469 50.135528564453125,6.350349426269531 C47.653289794921875,8.038009643554688 45.369842529296875,10.122665405273438 43.4833984375,12.406707763671875 z',
				'heartCurve': 'M7.44131,21.7771C2.97615,28.02938 0.39599,35.47269 -0.00082,43.11353L0.09823,43.11353C-0.29858,51.45011 1.48795,59.7861 5.15949,67.2294L5.15949,67.32905C8.03741,73.18452 11.70895,78.64318 15.87753,83.6048C19.64871,88.17019 23.8167,92.43666 28.1834,96.30748C31.55777,99.38351 35.13025,102.16295 38.80179,104.94122C40.68738,106.23072 42.47391,107.61927 44.35833,108.90993L44.25928,108.90993C47.7327,111.68879 50.80873,115.06258 53.38948,118.7347C58.05393,125.18627 61.0309,132.72746 61.92416,140.66664C71.25306,137.49097 80.18456,133.0258 88.32185,127.4675C107.2774,115.06258 122.36096,97.69603 131.88798,77.25229L131.98762,77.25229C137.04888,67.0313 139.92679,55.91586 140.62078,44.50326L140.62078,44.60173C141.01818,36.3648 139.43034,28.12845 135.95691,20.58609C133.17747,14.63097 128.911,9.57088 123.55199,5.60041C119.38458,2.42474 114.4212,0.53915 109.16183,0.04328C99.23683,-0.45317 89.51288,3.31801 82.56603,10.66168C78.59673,14.82967 75.22236,19.69165 72.54139,24.85255L72.64044,24.85255C71.44942,27.23401 70.35745,29.61606 69.46477,32.09657L69.36572,32.09657C67.57918,29.3183 65.59453,26.6385 63.51024,24.05893C59.14414,18.69932 53.98323,14.23357 48.0287,10.76073C41.47925,6.79144 33.54007,5.79853 26.19582,8.18057L26.29488,8.18057C18.7531,10.66168 12.20306,15.42518 7.53861,21.87558',
				"star_points_5": "M-0.00082,51.04887L53.73506,51.04887L70.33986,0.00001L86.94467,51.04887L140.68054,51.04887L97.20738,82.59845L113.81304,133.64731L70.33986,102.09688L26.86669,133.64731L43.47235,82.59845L-0.00082,51.04887z",
				"cloud": "M85.61639,0.00677C85.31163,0.01664 85.01769,0.04251 84.71152,0.07308C79.81985,0.55985 75.50195,3.66482 73.30891,8.29077L72.14537,11.79221C72.39604,10.57882 72.78216,9.40211 73.30891,8.29077C69.39689,4.23813 63.90651,2.36771 58.48151,3.24484C53.05651,4.1229 48.34497,7.64316 45.77003,12.74458C38.50142,8.26819 29.44047,8.53956 22.43101,13.45193S11.68542,27.06123 12.82074,35.83717L13.56383,39.40304C13.22803,38.2348 12.97735,37.04586 12.82074,35.83717L12.70787,36.16591C6.61456,36.83046 1.61707,41.55705 0.31949,47.88411S1.72054,60.70329 7.00633,63.97522L15.25976,65.98014C12.37771,66.21765 9.50037,65.51924 7.00633,63.97522C2.92923,68.54238 1.99096,75.30353 4.68065,80.88749C7.37034,86.47146 13.11374,89.68648 19.03962,88.9575L22.64171,88.00419C21.48287,88.48908 20.27935,88.80512 19.03962,88.9575C22.40326,95.15005 27.98158,99.66688 34.52921,101.46487C41.07684,103.26286 48.03175,102.20043 53.83017,98.52309C58.561,105.98312 66.95694,109.90172 75.37686,108.58157C83.79631,107.26188 90.74181,100.93201 93.20811,92.34325L94.06407,87.54423C93.93662,89.17478 93.65772,90.77806 93.20811,92.34325C98.99901,96.14098 106.28408,96.35497 112.26686,92.90197C118.25059,89.44897 121.98389,82.86513 122.03892,75.69388L119.89337,64.97933L111.44288,57.72953C117.97687,61.82262 122.0963,68.0589 122.03845,75.69388C129.80793,75.77524 136.40776,69.11756 139.36882,61.47694C142.32988,53.83585 141.21007,45.12481 136.4294,38.58189C138.41221,33.70997 138.29464,28.16786 136.10629,23.39518C133.91748,18.62155 129.8813,15.07402 125.02584,13.68144C123.93943,7.35533 119.46069,2.2666 113.54187,0.59936C107.62304,-1.06882 101.32092,0.98783 97.3581,5.89126L94.95154,9.95143C95.56623,8.48266 96.36999,7.115 97.3581,5.89126C94.56494,2.03756 90.18356,-0.13762 85.61639,0.00677L85.61639,0.00677z"

			};

			var filter_lib = {
				'css_brightness': '<filter id="sample_css_brightness"  type="css_brightness" color-interpolation-filters="sRGB"><feComponentTransfer> <feFuncR type="linear" slope="0.5"/> <feFuncG type="linear" slope="0.5"/> <feFuncB type="linear" slope="0.5"/></feComponentTransfer></filter>',
				'brightness_threshold': '<filter id="sample_brightness_threshold" type="brightness_threshold" color-interpolation-filters="sRGB"><feComponentTransfer> <feFuncR type="linear" slope="1.5" intercept="-0.3"/> <feFuncG type="linear" slope="1.5" intercept="-0.3"/> <feFuncB type="linear" slope="1.5" intercept="-0.3"/></feComponentTransfer> </filter>',
				'css_invert': '<filter id="sample_css_invert" type="css_invert" color-interpolation-filters="sRGB"><feComponentTransfer>  <feFuncR type="table" tableValues="1 0"/>  <feFuncG type="table" tableValues="1 0"/>  <feFuncB type="table" tableValues="1 0"/></feComponentTransfer></filter>',
				'gamma_correct': '<filter id="sample_gamma_correct" type="gamma_correct" color-interpolation-filters="sRGB"><feComponentTransfer> <feFuncG type="gamma" amplitude="1" exponent="0.5"/> </feComponentTransfer> </filter>',
				'gamma_correct2': '<filter id="sample_gamma_correct2" type="gamma_correct2" color-interpolation-filters="sRGB"><feComponentTransfer> <feFuncG type="gamma" amplitude="1" exponent="0.5" offset="-0.1"/> </feComponentTransfer> </filter>',
				'css_grayscale': '<filter id="sample_css_grayscale" type="css_grayscale" color-interpolation-filters="sRGB"><feColorMatrix type="saturate" values="0"/></filter>',
				'css_sharpen': '<filter id="sample_css_sharpen" type="css_sharpen" color-interpolation-filters="sRGB"><feConvolveMatrix order="3" kernelMatrix="1 0 0 0 1 0 0 0 -1"/></filter>',
				'css_hue_rotate': '<filter id="sample_css_hue_rotate" type="css_hue_rotate" color-interpolation-filters="sRGB"><feColorMatrix type="hueRotate" values="180"/></filter>',
				'luminance_mask': '<filter id="sample_luminance_mask" type="luminance_mask" color-interpolation-filters="sRGB"><feColorMatrix type="luminanceToAlpha"/></filter>',
				'css_sepia': '<filter id="sample_css_sepia" type="css_sepia" color-interpolation-filters="sRGB"><feColorMatrix type="matrix" values=".343 .669 .119 0 0             .249 .626 .130 0 0            .172 .334 .111 0 0            .000 .000 .000 1 0 "/></filter>',
				'dusk': '<filter id="sample_dusk" type="dusk" color-interpolation-filters="sRGB"><feColorMatrix type="matrix" values="1.0 0   0   0   0            0   0.2 0   0   0             0   0   0.2 0   0             0   0   0   1.0 0 "/></filter>',
			};

			var color_filter_lib = {
				'oneColor': '<filter id="oneColor" type="oneColor" color-interpolation-filters="sRGB" ><feComponentTransfer><feFuncR slope="0" type="linear"/><feFuncG slope="0" type="linear"/><feFuncB slope="0" type="linear"/></feComponentTransfer><feComponentTransfer><feFuncR type="table" tableValues=""/><feFuncG type="table" tableValues=""/><feFuncB type="table" tableValues=""/></feComponentTransfer></filter>',
				'twoColor': '<filter id="twoColor" type="twoColor" color-interpolation-filters="sRGB" ><feColorMatrix in = "SourceGraphic" type = "saturate" values = "0"/><feComponentTransfer ><feFuncR slope="1" type="linear"/><feFuncG slope="1" type="linear"/><feFuncB slope="1" type="linear"/></feComponentTransfer><feComponentTransfer><feFuncR intercept="-1" slope="10" type="linear"/><feFuncG intercept="-1" slope="10" type="linear"/><feFuncB intercept="-1" slope="10" type="linear"/></feComponentTransfer><feComponentTransfer><feFuncR type="table" tableValues=""/><feFuncG type="table" tableValues=""/><feFuncB type="table" tableValues=""/></feComponentTransfer></filter>',
			};

			var baseSVG = '<div class="imageeffectpreviw"><svg width="300" height="150" x="0" y="63" viewBox="0 0 300 150" overflow="hidden"><g mask="" style="pointer-events:all"><g type="photobox" style="pointer-events:inherit"><g><image xmlns:xlink="http://www.w3.org/1999/xlink" x="0" y="0" width="300" height="150" preserveAspectRatio="none" id="svg_5" xlink:href="" style="pointer-events:inherit"/></g></g></g></svg><div>';

			var imageEffectbtn, maskLabel, effectLabel, iePropertyPanel, sourceSvg, sourceImage, boolMousedown = false, startPosX, startPosY, resizeDiv, offsetLeft, offsetTop, updateBtn, cancelBtn, selectedElem, ieStrokeColorPicker, strokeSlider, btnClicked;
			var checkbox, pickerAry = [], pickerNamePrefix = "effect_color_", colorRadios = [];

			var init = function () {

				var effectWindow = '<div id="imageEffect_window" style="display:none;">'+
					'<div class="global_overlay"></div>'+
					'<div class="global_popup_box">'+
					'<div class="toolbar_button" >'+
					'<button type="button" class="close-window-positoin">x</button>'+
					'</div>'+
					'<p class="new-heading pop-heading-line" id="imageEffectPopupCaption">'+ langData[currentStore].adjustImage +'</p>'+
					'<div>'+
					'<div>'+
					'<div id="ie_preview"></div>'+
					'<div class="filters-button">'+
					//   '<div class="tabCon"> <span id="tabShapes">Mask</span> <span id="tabFilters">Effects</span> <span id="tabColors">Custom Effects</span> '+'</div>'+
					'<div class="clearer">&nbsp;</div>'+
					'<div>'+
					//'<div id="ie_shapes" class="photo_filters_option"></div>'+
					'<div id="ie_filters" class="photo_filters_option" style="padding-top: 10px;padding-bottom: 10px;"></div>'+
					// '<div id="ie_colorChanger" class="photo_filters_option" style="display:none;">       '+
					//   '<div class="colorCon"> </div>'+
					// '</div>'+
					'</div>'+
					'</div>'+
					'<div class="buttonHolder"> </div>'+
					'</div>'+
					'</div>'+
					'</div>'+
					'</div>';
				var quickeditarea = document.getElementById("quickeditarea");
				if(quickeditarea != null){
					quickeditarea.parentNode.appendChild(jQuery(effectWindow)[0]);
				}


				jQuery("#imageEffect_window .close-window-positoin").bind("click", cancelBtnClickHandler);

				//adding default svg into html
				jQuery("#ie_preview").html(baseSVG);
				//adding button in property panel
				iePropertyPanel = jQuery('<div class="imageEffectPanel caption-section"></div>');
				jQuery(".image_effect").append(iePropertyPanel);
				maskLabel = jQuery('<div>Mask:None</div>');
				iePropertyPanel.append(maskLabel);
				effectLabel = jQuery('<div>Effect:None</div>');
				iePropertyPanel.append(effectLabel);
				// imageEffectbtn = jQuery('<button type="button" style="" class="generalbutton">' + uiStrings.elementLabel.imageEffectBtnCaption + '</button>');
				// iePropertyPanel.append(imageEffectbtn);

				// if (svgedit.browser.isTouch() && svgedit.browser.isIOS() == false) {//for android
				// 	imageEffectbtn.on("touchstart", showPanel);
				// } else {
				// 	imageEffectbtn.on("click", showPanel);
				// }

				//adding buttons in image effect window

				updateBtn = jQuery('<button type="button" style="" class="prcadcart button"><span>'+langData[currentStore].adjust+'</span></button>');
				cancelBtn = jQuery('<button type="button" style="" class="prcadcart button"><span>'+langData[currentStore].dontAdjust+'</span></button>');
				jQuery("#imageEffect_window .buttonHolder").append(updateBtn);
				jQuery("#imageEffect_window .buttonHolder").append(cancelBtn);
				updateBtn.bind("click", updateBtnClickHandler);
				cancelBtn.bind("click", cancelBtnClickHandler);

				sourceSvg = jQuery("#ie_preview svg");
				sourceImage = sourceSvg.find("image")[0];


				//adding shapes
				var shapeContainer = jQuery("#ie_shapes");
				var noEffect = jQuery('<i class="fa fa-ban fa-3"></i>');
				shapeContainer.append(noEffect);
				noEffect.bind("click", removeClipPath);
				for (var type in shape_lib) {
					var div = jQuery('<div class="tool_button" >');
					div.append('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><svg viewBox="-25 -15 168 168"><path fill="none" stroke="#000000" stroke-width="5" d="' + shape_lib[type] + '"/></svg></svg>');
					div.bind('click', { shapeType: type }, shapeClickHandler);
					shapeContainer.append(div);
				}

				var resizeCon = jQuery('<div class="propo_label">');
				checkbox = jQuery('<input type="checkbox" checked="checked" class="checkboxStepper" name="propCheckbox">');
				resizeCon.append(checkbox);
				resizeCon.append('<label id="proportionCaption">Proportion</label>');
				shapeContainer.append(resizeCon);


				//adding stroke controls
				var strokeConStr = '<div class="ie_stroke_panel range-blocks">' +
					'<div class="caption-section">' +
					'<div id="IEBorderCaption" class="caption">Border</div>' +
					'<input autocomplete="off" id="ie_strokeSlider_label" class="slider_itag" size="2" value="0" data-attr="Stroke Width" disabled="disabled" type="text">' +
					'<div class="psright">' +
					'<div class="left">' +
					'<label class="stroke_tool no-padding">' +
					'<select autocomplete="off" id="ie_stroke_style" >' +
					'<option selected="selected" value="none">—</option>' +
					'<option value="2,2">...</option>' +
					'<option value="5,5">- -</option>' +
					'<option value="5,2,2,2">- .</option>' +
					'<option value="5,2,2,2,2,2">- ..</option>' +
					'</select>' +
					'</label>' +
					'</div>' +
					'<div class="ie_colorChanger_picker color_tool"></div>' +
					'</div></div>' +
					'<div class="ranger-area"><div id="ie_strokeSlider"></div></div>' +
					'</div>';
				var strokeCon = jQuery(strokeConStr);
				shapeContainer.append(strokeCon);
				// strokeSlider = jQuery("#ie_strokeSlider").slider({
				// 	range: false,
				// 	min: 0,
				// 	max: 10,
				// 	step: 1,
				// 	value: 0,
				// 	slide: function (event, ui) { updateStrokeWidth(ui.value); },
				// 	start: function (event, ui) { },
				// 	stop: function (event, ui) { updateStrokeWidth(ui.value); }
				// });

				jQuery("#ie_stroke_style").bind("change", strokeOptionChangeHandler);

				var pickerCon = jQuery(".ie_colorChanger_picker");
				//pickerCon.append("<span>Colors</span>");
				//strokeCon.append(pickerCon);
				var pickerId = "ie_stroke_colorPicker";
				var cpString = '<div class="color_block_parent" style="display:block;">';
				cpString += '<div><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="22" height="25" xmlns:xlink="http://www.w3.org/1999/xlink" class="svg_icon"><svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">		  <line fill="none" stroke="#d40000" id="svg_90" y2="24" x2="24" y1="0" x1="0"/>		  <line id="svg_92" fill="none" stroke="#d40000" y2="24" x2="0" y1="0" x1="24"/>	</svg></svg></div>';
				cpString += '<div id="' + pickerId + '" class="color_block"></div></div>';
				pickerCon.append(jQuery(cpString));
				var paintbox = jQuery('#' + pickerId);

				// ieStrokeColorPicker = new PaintBox("#" + pickerId, "#000000");
				paintbox.click(function () {
					if (pickerMode == "full") {
						colorPicker(jQuery(this));
					} else if (pickerMode == "printable") {
						simplePicker(jQuery(this), hexCodeList);
					}
				});

				resizeDiv = jQuery('<div style="position:absolute; width:25px; height:25px;display:none;cursor: pointer;" class="fa fa-arrows-alt">');
				jQuery("#ie_preview").append(resizeDiv);
				if(isTouch()){
					resizeDiv.on("touchstart", startResizeObject);
				}else{
					resizeDiv.on("mousedown", startResizeObject);
				}

				//adding filters
				var filterContainer = jQuery("#ie_filters");

				var noEffect = jQuery('<div title="'+langData[currentStore].filterlabel.original+'" class="filter_button" >');
				noEffect.append('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50"><svg viewBox="0 0 50 50"><title>' + langData[currentStore].filterlabel.original + '</title><defs></defs><image width="50" height="50" xlink:href="" /></svg></svg>');
				noEffect.append('<span>' + langData[currentStore].filterlabel.original + '</span>');
				filterContainer.append(noEffect);
				noEffect.bind("click", removeFilter);

				for (var type in filter_lib) {
					var div = jQuery('<div title="'+langData[currentStore].filterlabel[type]+'" class="filter_button" >');
					div.append('<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50"><svg viewBox="0 0 50 50"><title>' + langData[currentStore].filterlabel[type] + '</title><defs>' + filter_lib[type] + '</defs><image width="50" height="50" filter="url(#sample_' + type + ')" xlink:href="" /></svg></svg>');
					div.append('<span>' + langData[currentStore].filterlabel[type] + '</span>');
					div.bind('click', { filterType: type }, filterClickHandler);
					filterContainer.append(div);
				}
				//var noEffect = jQuery('<i title="'+langData[currentStore].filterlabel.original+'" class="fa fa-ban fa-3"></i>');
				//filterContainer.append(noEffect);
				//noEffect.bind("click", removeFilter);

				//adding color change feature
				var colorfilterContainer = jQuery("#ie_colorChanger div:first");
				var noEffect = colorfilterContainer.append('<div><input type="radio" name="colorOption" id="radio_original" value="0" checked="checked" /><span>' + langData[currentStore].filterlabel.original + '</span></div>');

				var cnt = 0;
				for (var type in color_filter_lib) {
					cnt++;
					var div = jQuery('<div><input type="radio" name="colorOption" id=radio_' + type + ' value="' + cnt + '" ><span>' + langData[currentStore].filterlabel[type] + '</span></input></div>');
					div.append('<svg display="none" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50"><svg viewBox="0 0 50 50"><title>' + type + '</title><defs>' + color_filter_lib[type] + '</defs><image width="50" height="50" filter="url(#' + type + ')" xlink:href="" /></svg></svg>');
					//div.bind('change', { filterType: type }, colorRadioChangeHandler);
					colorfilterContainer.append(div);

				}
				//var noEffect = jQuery('<i class="fa fa-ban fa-3"></i>');
				//colorfilterContainer.append(noEffect);
				//noEffect.bind("click", removeFilter);

				createPicker(cnt);
				colorRadios = jQuery('#ie_colorChanger').find("input");
				colorRadios.bind("change", colorRadioChangeHandler);

				//tabs
				// jQuery("#tabShapes").addClass("active");
				// jQuery("#tabShapes").bind("click", showTab);
				// jQuery("#tabFilters").bind("click", showTab);
				// jQuery("#tabColors").bind("click", showTab);
				if(isTouch()){
					filterContainer.parent().css("overflow","auto");
					jQuery(".global_popup_box").css("top","30%");
					// jQuery(".global_popup_box").css("max-width","none");
					sourceSvg.css("height", (jQuery(window).height() - 270) + "px");
					jQuery(".global_popup_box").css("position","absolute");
				}else if(isIE()){
					jQuery(".global_popup_box").css("top","10%");
					jQuery(".global_popup_box").css("width","50%");
					jQuery(".global_popup_box").css("position","absolute");
				}else{
					jQuery(".global_popup_box").css("position","absolute");
					jQuery(".global_popup_box").css("top","10%");
				}
			};

			function applyFilterOnImage(imageObject, imagesrc, origWidth, origHeight, filtertype, sideCnt ){
				var filter = (filtertype == "oneColor" || filtertype == "twoColor") ? color_filter_lib[filtertype] : filter_lib[filtertype];
				var filtername = (filtertype == "oneColor" || filtertype == "twoColor") ? filtertype : "sample_"+filtertype;
				var svg = '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="375" height="280" x="0" y="63" viewBox="0 0 375 280" overflow="hidden"><defs>'+ filter +'</defs><g mask="" style="pointer-events:all"><g type="photobox" style="pointer-events:inherit"><g><image x="0" y="0" width="375" height="280" preserveAspectRatio="none"  xlink:href="'+imagesrc+'" style="pointer-events:inherit" filter="url(#'+filtername+')" /></g></g></g></svg>';
				if(filtertype == "oneColor" || filtertype == "twoColor"){
					var colors = imageObject.getAttribute("colors").split(",");
					var parser = new DOMParser();
					var doc = parser.parseFromString(svg, "image/svg+xml");
					var defs = findDefs(doc);
					for(var cnt=0; cnt < colors.length; cnt++ ){
						remapColor("#"+colors[cnt], cnt, defs);
					}
					svg = (new XMLSerializer()).serializeToString(doc);
				}
				var text_svg = setWidthHeight(svg, origWidth, origHeight);
				var currentTime = new Date().getTime();
				
				jQuery('body').trigger('processStart');
				
				jQuery.ajax({
					type: 'POST',
					url: generatePreviewPngUrl,
					cache: false,
					async: true,
					data: "svg=" + encodeURIComponent(text_svg) + "&side=0" + "&current_time=" + currentTime + "&form_key="+formkey + "&oldimagepath=&action=filterimage&type=filterimage",
					//data: "svg=" + encodeURIComponent(text_svg) + "&side=0" + "&current_time=" + currentTime + "&form_key="+formkey + "&oldimagepath="+oldimagepath+ "&action=filterimage&type=filterimage",
					success: function (image) {
						console.log("filtered image");
						console.log(image);
						if(sideCnt >= 0){
							updateObjectInPage(imageObject, "xlink:href", image, sideCnt);
						}else{
							setHref(imageObject, image);
						}
						jQuery('body').trigger('processStop');
					},
					error:function (html) {jQuery.alert('error in saving'); jQuery('body').trigger('processStop');}
				});
			}

			function setWidthHeight(str, origWidth, origHeight){
				var parser = new DOMParser();
				var doc = parser.parseFromString(str, "image/svg+xml");
				// var origWidth = sourceImage.getAttribute("origWidth");
				// var origHeight = sourceImage.getAttribute("origHeight");
				doc.documentElement.setAttribute("width", origWidth);
				doc.documentElement.setAttribute("height", origHeight);
				doc.documentElement.setAttribute("viewBox", "0 0 " + origWidth + " " + origHeight);
				var images = doc.getElementsByTagName("image");
				var imageCnt = images.length;
				while(imageCnt--){
					images[imageCnt].setAttribute("width",origWidth);
					images[imageCnt].setAttribute("height",origHeight);
					images[imageCnt].removeAttribute("isAdminUploaded");
					images[imageCnt].parentNode.removeAttribute("clip-path");
				}
				// doc.documentElement.removeAttribute("viewBox");
				// if(!svgedit.browser.isSafari()){
				// 	doc.documentElement.setAttribute("xmlns", "http://www.w3.org/2000/svg");
				// }
				var paths = doc.getElementsByTagName("path");
				var pathCnt = paths.length;
				while(pathCnt--){
					paths[pathCnt].parentNode.removeChild(paths[pathCnt]);
				}
				str = (new XMLSerializer()).serializeToString(doc);
				return str;
			}

			function updateImageLabel(evt) {
				var selectedElements = svgCanvas.getSelectedElems();
				if (selectedElements.length == 1 && selectedElements[0].getAttribute("type") == "photobox") {
					selectedElements[0].setAttribute("label", evt.target.value);
				}
			}

			function loadImageinPhotobox(photobox, imageSrc, orgWidth, orgHeight) {
				//console.log("photobox = " + photobox);
				imageObj = photobox.childNodes[0].childNodes[0];
				var imageHeightWidth = getProportionalWidthHeight(orgWidth, orgHeight, imageObj.getAttribute("width"), imageObj.getAttribute("height"));
				//imageObj.setAttribute("preserveAspectRatio", "xMidYMid");
				var oldImageSizeInPreviewDialog = getSizeToFit(imageObj);
				var newImageSizeInPreviewDialog = getSizeToFit(null, orgWidth, orgHeight);
				var ratioWidth = imageObj.getAttribute("width") / oldImageSizeInPreviewDialog[0];
				var ratioHeight = imageObj.getAttribute("height") / oldImageSizeInPreviewDialog[1];

				setHref(imageObj, imageSrc);
				//imageObj.setAttribute("width", newImageSizeInPreviewDialog[0]*ratioWidth);
				//imageObj.setAttribute("height", newImageSizeInPreviewDialog[1]*ratioHeight);
				imageObj.setAttribute("width", imageHeightWidth[0]);
				imageObj.setAttribute("height", imageHeightWidth[1]);
				if(imageObj.hasAttribute("origwidth")){
					imageObj.removeAttribute("origwidth");
				}
				if(imageObj.hasAttribute("origheight")){
					imageObj.removeAttribute("origheight");
				}
				imageObj.setAttribute("origwidth", orgWidth);
				imageObj.setAttribute("origheight", orgHeight);
				imageObj.setAttribute("orighref", imageSrc);
				if(imageObj.hasAttribute("isadminuploaded")){
					imageObj.removeAttribute("isadminuploaded");
				}
				if (imageObj.getAttribute("templateSrc") == imageSrc) {
					imageObj.setAttribute("isAdminUploaded", "true");
				} else {
					imageObj.setAttribute("isAdminUploaded", "false");
				}
				showImageTab();
				if(imageObj.getAttribute("filtertype") && imageObj.getAttribute("filtertype") != "" && applyFilter){
					imageEffect.applyFilterOnImage(imageObj, image.src, orgWidth, orgHeight,imageObj.getAttribute("filtertype"));
				}
				// svgCanvas.runExtensions("elementChanged", {
				// 	elems: [photobox]
				// });
			}

			function removeImage(selected) {
				var templateSrc = selected.childNodes[0].childNodes[0].getAttribute("templateSrc");
				var image = new Image();
				image.onload = function (e) {
					loadImageinPhotobox(selected, templateSrc, e.target.naturalWidth, e.target.naturalHeight);
					if (svgCanvas.getPrivateMethods().extensions.undoRedo) {
						svgCanvas.getPrivateMethods().extensions.undoRedo.addToStack();
					}
				}
				image.src = templateSrc;
			}

			function imageEffectCaptionHandler() {
				jQuery("#printQualityCaption").removeClass("active");
				jQuery(this).addClass("active");
				jQuery(".upload-mathod").hide();
				iePropertyPanel.show();
			}
			function printQualityCaptionHandler() {
				jQuery("#imageEffectCaption").removeClass("active");
				jQuery(this).addClass("active");
				jQuery(".upload-mathod").show();
				iePropertyPanel.hide();
			}

			function updatePropertyPanel(elem) {
				var mask, filter;
				if (user == "admin") {
					jQuery('#imageLabelCaption').hide();
					jQuery('#imageLabel').hide();
				}
				if (elem.tagName == "g" && elem.getAttribute("type") == "photobox") {
					var cp = elem.childNodes[1];
					//mask = cp.cloneNode(true);
					//svgCanvas.getContentElem().appendChild(mask);
					//var bbox = svgedit.utilities.getBBox(mask);
					//svgCanvas.moveSelectedElements((-bbox.x )*zoom,(-bbox.y)*zoom,false, [mask]);
					var shapeType = cp.getAttribute("shapeType");
					mask = shape_lib[shapeType];
					filter = elem.childNodes[0].childNodes[0].getAttribute("filter");
					if (user == "admin") {
						jQuery('#imageLabelCaption').show();
						jQuery('#imageLabel').show();
						jQuery("#imageLabel").val(elem.getAttribute("label") || "");
					}
				} else if (elem.tagName == "image") {
					filter = elem.getAttribute("filter");
				}
				if (filter) {
					filter = filter.replace(/"/g, ''); // for ie
					filter = filter.substring(5, filter.indexOf(")"));
					//console.log("filter = " + filter);
					//if(filter.indexOf("twoColor") > -1){
					//filter = uiStrings.filterlabel["twoColor"]
					//}else if(filter.indexOf("oneColor") > -1){
					//filter = uiStrings.filterlabel["oneColor"]
					//}else{
					if (svgCanvas.getElem(filter)) {
						filter = langData[currentStore].filterlabel[svgCanvas.getElem(filter).getAttribute("type")];
					} else {
						filter = "none";
					}
					//}

				} else {
					filter = "none";
				}
				if (mask) {
					mask = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"><svg viewBox="-25 -15 168 168"><path fill="none" stroke="#000000" stroke-width="5" d="' + mask + '"/></svg></svg>';
				} else {
					mask = "none";
				}
				// maskLabel.html(langData[currentStore].elementLabel.MaskCaption + ': ' + mask);
				// effectLabel.html(langData[currentStore].elementLabel.EffectCaption + ': ' + filter);
				//console.log(mask);
				//console.log(filter);
			}

			function remapColor(color, index, defs) {
				//setting color to image as attribute
				if(!defs){
					var colors = sourceImage.getAttribute("colors").split(",");
					colors[index] = color.substring(1);
					sourceImage.setAttribute("colors", colors.join(","));
				}

				var red = parseInt(color.substr(1, 2), 16);
				var defs = defs || findDefs(sourceSvg[0]);
				var funcR = jQuery(defs).find("feFuncR[type='table']")[0];
				var tableValues = funcR.getAttribute('tableValues').split(' ');
				tableValues[index] = red / 255;
				if (tableValues.length == 1) {
					tableValues.push(red / 255);
				}

				funcR.setAttribute('tableValues', tableValues.join(' '));

				var green = parseInt(color.substr(3, 2), 16);
				var funcG = jQuery(defs).find("feFuncG[type='table']")[0];
				var tableValues = funcG.getAttribute('tableValues').split(' ');
				tableValues[index] = green / 255;
				if (tableValues.length == 1) {
					tableValues.push(green / 255);
				}
				funcG.setAttribute('tableValues', tableValues.join(' '));

				var blue = parseInt(color.substr(5, 2), 16);
				var funcB = jQuery(defs).find("feFuncB[type='table']")[0];
				var tableValues = funcB.getAttribute('tableValues').split(' ');
				tableValues[index] = blue / 255;
				if (tableValues.length == 1) {
					tableValues.push(blue / 255);
				}
				funcB.setAttribute('tableValues', tableValues.join(' '));
			}


			function createPicker(pickerCount) {
				var pickerCon = jQuery("<div class='ie_colorChanger_picker color_tool'>");
				//pickerCon.append("<span>Colors</span>");
				jQuery('#ie_colorChanger .colorCon').append(pickerCon);
				for (var cnt = 0; cnt < pickerCount; cnt++) {
					var cpString = '<div class="color_block_parent" style="display:none;">';
					cpString += '<div><svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="22" height="25" xmlns:xlink="http://www.w3.org/1999/xlink" class="svg_icon"><svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">		  <line fill="none" stroke="#d40000" id="svg_90" y2="24" x2="24" y1="0" x1="0"/>		  <line id="svg_92" fill="none" stroke="#d40000" y2="24" x2="0" y1="0" x1="24"/>	</svg></svg></div>';
					cpString += '<div id="' + pickerNamePrefix + pickerAry.length + '" class="color_block"></div></div>';
					pickerCon.append(jQuery(cpString));

					var clr = jQuery(this).attr('fill');
					if (clr == null) {
						clr = "#000000";
					}
					var paintbox = jQuery('#' + pickerNamePrefix + pickerAry.length);

					// pickerAry[pickerAry.length] = new PaintBox("#" + paintbox[0].id, clr);
					paintbox.click(function () {
						if (pickerMode == "full") {
							colorPicker(jQuery(this));
						} else if (pickerMode == "printable") {
							simplePicker(jQuery(this), hexCodeList);
						}
					});
				}
			}

			var colorPicker = function (elem) {
				//trace(elem.attr('id'));
				var id = elem.attr('id');
				var pickerIndex = id.substr(pickerNamePrefix.length, id.length - pickerNamePrefix.length);
				//var opacity = (picker == 'stroke' ? jQuery('#stroke_opacity') : jQuery('#fill_opacity'));
				// trace('pickerIndex = ' + pickerIndex);
				var paint;
				if (id == "ie_stroke_colorPicker") {
					paint = ieStrokeColorPicker.paint;
				} else {
					paint = pickerAry[pickerIndex].paint;
				}
				var title = 'Pick a Fill Paint and Opacity';
				var pos = elem.offset();
				jQuery("#color_picker")
					.draggable({ cancel: '.jGraduate_tabs, .jGraduate_colPick, .jGraduate_gradPick, .jPicker', containment: 'window' })
					.css(svgEditor.curConfig.colorPickerCSS || { 'right': 72, 'top': 100 })
					.jGraduate(
						{
							paint: paint,
							window: { pickerTitle: title },
							images: { clientPath: svgEditor.curConfig.jGraduatePath },
							newstop: 'inverse'
						},
						function (p) {
							paint = new jQuery.jGraduate.Paint(p);
							if (id == "ie_stroke_colorPicker") {
								ieStrokeColorPicker.setPaint(paint);
							} else {
								pickerAry[pickerIndex].setPaint(paint);
							}
							var clr = paint[paint.type];
							if (clr != "none" && clr.indexOf("#") == -1) {
								clr = "#" + clr;
							}

							//code to change the image color
							//elemAry[pickerIndex].attr('fill', clr);
							if (id == "ie_stroke_colorPicker") {
								updateStrokeColor(clr);
							} else {
								remapColor(clr, pickerIndex);
							}
							//code to change the image color


							jQuery('#color_picker').hide();
							svgCanvas.runExtensions("elementChanged", {
								elems: svgCanvas.getSelectedElems()
							});
						},
						function (p) {
							jQuery('#color_picker').hide();
						});
				//hide the gradient tabs, if client wants then we will comment this code
				jQuery('.jGraduate_tab_lingrad').hide();
				jQuery('.jGraduate_tab_radgrad').hide();
			};

			var simplePicker = function (elem, colorList) {
				var id = elem.attr('id');
				var pickerIndex = id.substr(pickerNamePrefix.length, id.length - pickerNamePrefix.length);
				$(elem).simpleColor({
					boxHeight: 40,
					cellWidth: 20,
					cellHeight: 20,
					columns: 10,
					colors: colorList,
					chooserCSS: { 'left': jQuery(elem).offset().left - jQuery(".right-panel-property").offset().left + 20 },
					displayCSS: { 'border': '1px solid red' },
					displayColorCode: true,
					livePreview: false,
					onSelect: function (hex, element) {
						//alert("You selected #" + hex + " for input #" + element.attr('class'));
						//var paint = paintBox[picker].paint;
						var paint = new jQuery.jGraduate.Paint({ solidColor: hex });
						if (id == "ie_stroke_colorPicker") {
							ieStrokeColorPicker.setPaint(paint);
						} else {
							pickerAry[pickerIndex].setPaint(paint);
						}
						//code to change the image color
						//elemAry[pickerIndex].attr('fill', "#" + paint[paint.type]);
						if (id == "ie_stroke_colorPicker") {
							updateStrokeColor("#" + paint[paint.type]);
						} else {
							remapColor("#" + paint[paint.type], pickerIndex);
						}

						//code to change the image color
						svgCanvas.runExtensions("elementChanged", {
							elems: svgCanvas.getSelectedElems()
						});
					}/*,
					 onCellEnter: function(hex, element) {
					 console.log("You just entered #" + hex + " for input #" + element.attr('class'));
					 },
					 onClose: function(element) {
					 alert("color chooser closed for input #" + element.attr('class'));
					 }*/
				});
				setHideOnClick(jQuery(".simpleColorChooser"));
			}



			function updateStrokeColor(clr) {
				var gtag = sourceImage.parentNode.parentNode;
				if (gtag.childNodes.length == 2) {
					var path = gtag.childNodes[1];
					path.setAttribute("stroke", clr);
				}
			}

			function strokeOptionChangeHandler(evt) {
				var dashArray = evt.target.options[evt.target.selectedIndex].value;
				//console.log(dashArray);
				var gtag = sourceImage.parentNode.parentNode;
				if (gtag.childNodes.length == 2) {
					var path = gtag.childNodes[1];
					path.setAttribute("stroke-dasharray", dashArray);
				}
			}

			function updateStrokeWidth(wid) {
				var gtag = sourceImage.parentNode.parentNode;
				if (gtag.childNodes.length == 2) {
					var path = gtag.childNodes[1];
					path.setAttribute("stroke-width", wid);
				}
				jQuery("#ie_strokeSlider_label").val(wid);
			}

			function colorRadioChangeHandler(evt) {
				//updateImageColor(evt.target, evt.data.filterType);
				updateImageColor(evt.target);
			}

			function updateImageColor(elem) {
				var noOfColors = elem.value;
				if (sourceSvg.find("filter").length) {
					sourceSvg.find("filter").remove();
				}

				var filter;
				//filter = color_filter_lib["oneColor"];
				filter = jQuery(elem).parent().find("filter");

				if (filter.length) {
					var newFilter = filter[0].cloneNode(true);
					newFilter.id = getNextId();
					findDefs(sourceSvg[0]).appendChild(newFilter);
					sourceImage.setAttribute("filter", "url(#" + newFilter.id + ")");
				} else {
					sourceImage.removeAttribute("filter");
				}

				var pickerIndex = 0;
				while (pickerIndex < pickerAry.length) {
					if (pickerIndex < noOfColors) {
						pickerAry[pickerIndex].container.parent().show();
						var paint = pickerAry[pickerIndex].paint;
						var clr = paint[paint.type];
						if (clr != "none" && clr.indexOf("#") == -1) {
							clr = "#" + clr;
						}
						remapColor(clr, pickerIndex);
					} else {
						pickerAry[pickerIndex].container.parent().hide();
					}
					pickerIndex++;
				}
			}

			function showTab(evt) {
				jQuery("#ie_shapes").hide();
				jQuery("#ie_filters").hide();
				jQuery("#ie_colorChanger").hide();
				jQuery("#tabShapes").removeClass("active");
				jQuery("#tabFilters").removeClass("active");
				jQuery("#tabColors").removeClass("active");
				if (evt.currentTarget.id == "tabShapes") {
					jQuery("#ie_shapes").show();
					jQuery("#tabShapes").addClass("active");
				} else if (evt.currentTarget.id == "tabFilters") {
					jQuery("#ie_filters").show();
					jQuery("#tabFilters").addClass("active");
				} else if (evt.currentTarget.id == "tabColors") {
					jQuery("#ie_colorChanger").show();
					jQuery("#tabColors").addClass("active");
				}
			}

			function getMouseXY(event) {
				var root_sctm = jQuery('#svgcontent g')[0].getScreenCTM().inverse();
				var current_zoom = svgCanvas.getZoom();
				var pt = svgedit.math.transformPoint(event.pageX, event.pageY, root_sctm);
				var mouse_x = pt.x * current_zoom;
				var mouse_y = pt.y * current_zoom;
				var x = mouse_x / current_zoom, y;
				var y;
				if (isTouch()) {
					y = mouse_y / current_zoom;
				} else {
					y = (mouse_y - jQuery(window).scrollTop()) / current_zoom;
				}
				var obj = new Object();
				obj.x = x;
				obj.y = y;
				return obj;
			}

			function findCollageObject(x, y) {

				var photoLayernode = findNodeWithTitleName(svgCanvas.getElem("svgcontent").childNodes, "Layer 1");
				var rect;
				var returnObject = null;
				if (photoLayernode != null) {
					var i = photoLayernode.childNodes.length;
					while (i--) {
						var value = photoLayernode.childNodes[i];
						if (value.id != "" && value.getAttribute("type") == "photobox" && jQuery(value).css("display") != "none") {
							var clipPath = value.childNodes[0].getAttribute("clip-path");
							//console.log("clipPath = " + clipPath);
							//console.log(value);
							if (clipPath && clipPath != "") {
								clipPath = clipPath.replace(/"/g, ''); // for ie
								var clipPathId = clipPath.substring(5, clipPath.indexOf(")")); //url(#svg_1_ClipPath)
								rect = svgedit.utilities.getBBox(jQuery("#" + clipPathId).children()[0]);
							} else {
								rect = svgedit.utilities.getBBox(jQuery('#' + value.id)[0]);
							}
							var zoom = svgCanvas.getZoom();
							//rect.x = rect.x*zoom;
							//rect.y = rect.y*zoom;
							//rect.width = rect.width*zoom;
							//rect.height = rect.height*zoom;

							//createDotAt(rect.x,rect.y);
							//createDotAt(rect.x+rect.width,rect.y+rect.height);

							if (withinRect(x, y, rect)) {
								if (value.getAttribute("type") == "photobox") {
									returnObject = value;
									break;
								} else {
									//returnObject = null;
								}
							}

						}
					};
				}
				return returnObject;
			}

			function withinRect(x, y, rect) {
				var bool = false;
				if (x > parseFloat(rect.x) && x < parseFloat(rect.x) + parseFloat(rect.width)) {
					if (y > parseFloat(rect.y) && y < parseFloat(rect.y) + parseFloat(rect.height)) {
						bool = true;
					}
				}
				return bool;
			}

			function getProportionalWidthHeight(imageWidth, imageHeight, collageBBoxWidth, collageBBoxHeight) {
				var newheight, newwidth;
				var ratio = imageWidth / imageHeight;
				if (imageWidth > imageHeight) {
					newheight = collageBBoxWidth / ratio;
					newwidth = collageBBoxWidth;

					if (newheight < collageBBoxHeight) {
						newheight = collageBBoxHeight;
						newwidth = newheight * ratio;
					}
				} else {
					newheight = collageBBoxHeight;
					newwidth = collageBBoxHeight * ratio;

					if (newwidth < collageBBoxWidth) {
						newwidth = collageBBoxWidth;
						newheight = newwidth / ratio;
					}
				}
				return [newwidth, newheight];
			}

			var dragImage = function (sel) {
				//console.log("position");
				//console.log(jQuery(sel));
				jQuery(sel).find("img").draggable({
					start: function () {
						reset = false;
					},
					stop: function () {
						reset = true;
					},
					opacity: 0.7,
					helper: "clone"
				});
			}

			function removeClipPath() {
				var gtag = sourceImage.parentNode;
				gtag.removeAttribute('clip-path');
				resizeDiv.hide();
				if (gtag.parentNode.childNodes.length == 2) {
					gtag.parentNode.removeChild(gtag.parentNode.childNodes[1]);
				}
			}

			function removeFilter() {
				//sourceImage.setAttribute('filter',"");
				sourceImage.removeAttribute('filter');
			}

			function getFilterFromCanvas(type) {
				var filter = null;
				var defs = findDefs(document.getElementById("svgcontent"));

				var filters = defs.getElementsByTagName("filter");
				for (var i = 0; i < filters.length; i++) {
					if (filters[i].getAttribute("type") == type) {
						filter = filters[i];
						break;
					}
				}
				return filter;
			}

			function updateBtnClickHandler(evt) {
				// var rot = svgCanvas.getRotationAngle(selectedElem);
				// if (rot) {
				// 	svgCanvas.setRotationAngle(0, true, selectedElem);
				// }
				var gtag = sourceImage.parentNode.parentNode;

				// var bbox = svgedit.utilities.getBBox(selectedElem);
				var contentDefs = findDefs(document.getElementById("svgcontent"));
				// var bbox = selectedElem.getBoundingClientRect();
				// var oldPathStr = selectedElem.getBoundingClientRect();;
				// var oldPathBbox = Raphael.pathBBox(oldPathStr);
				//getting bbox of path in popup
				var pathStr = gtag.childNodes[1].getAttribute("d");
				var pathBbox = Raphael.pathBBox(pathStr);
				//getting bbox of path in selected photobox
				var selectedPathStr = selectedElem.getAttribute("pathstr");
				var selectedPathBbox = Raphael.pathBBox(selectedPathStr);
				//console.log("bbox.width = " + bbox.width);
				var newId = selectedElem.id;
				console.log("pathBbox");
				console.log(pathBbox);
				console.log("selectedPathBbox");
				console.log(selectedPathBbox);
				//as both the bbox are in proportion
				var scale = pathBbox.width / selectedPathBbox.width;
				//getting ratio for set position of image
				var clipPathStr = selectedElem.childNodes[1].getAttribute("d");
				var clipPathBbox = Raphael.pathBBox(clipPathStr);
				var clipPathRatio = clipPathBbox.width / selectedPathBbox.width;//ratio of width of pathStr and width of clipPath on canvas

				// var diffX = selectedPathBbox.x - pathBbox.x;
				// var diffY = selectedPathBbox.y - pathBbox.y;
				var expectedX = (pathBbox.x * clipPathBbox.x) /selectedPathBbox.x;
				var expectedY = (pathBbox.y * clipPathBbox.y) /selectedPathBbox.y;

				var imgInSelectedElem;

				if (selectedElem.tagName == "g") {
					imgInSelectedElem = selectedElem.childNodes[0].childNodes[0];
				} else {
					imgInSelectedElem = selectedElem;
				}

				console.log("scale");
				console.log(scale);

				var transform = 's' + scale + ',' + scale;
				var newPathStr = Raphael.transformPath(clipPathStr, transform);
				selectedElem.childNodes[1].setAttribute('d',newPathStr);
				clipPathBbox = Raphael.pathBBox(newPathStr);//update path again to get latest position
				// var diffX = expectedX - clipPathBbox.x ;
				// var diffY = expectedY - clipPathBbox.y ;
				var imgSizeRatio = parseFloat(imgInSelectedElem.getAttribute("width")) / parseFloat(sourceImage.getAttribute("width")) ;
				var diffX = (parseFloat(imgInSelectedElem.getAttribute("x")) + pathBbox.x * imgSizeRatio) - clipPathBbox.x;
				var diffY = (parseFloat(imgInSelectedElem.getAttribute("y")) + pathBbox.y * imgSizeRatio) - clipPathBbox.y;
				console.log("diffX");
				console.log(diffX);
				console.log("diffY");
				console.log(diffY);

				transform = 't' + diffX + ',' + diffY;
				newPathStr = Raphael.transformPath(newPathStr, transform);
				selectedElem.childNodes[1].setAttribute('d', newPathStr);
				//getting path in defs
				var clipPathAttr = selectedElem.childNodes[0].getAttribute("clip-path");
				if (clipPathAttr && clipPathAttr != "") {
					clipPathAttr = clipPathAttr.replace(/"/g, ''); // for ie
					var clipPathId = clipPathAttr.substring(5, clipPathAttr.indexOf(")")); //url(#svg_1_ClipPath)
					var clippathInDefs = document.getElementById(clipPathId);
					if (clippathInDefs) {
						clippathInDefs.childNodes[0].setAttribute("d", newPathStr);
					}
				}

				//to keep the photobox in same position move the whole photobox
				moveSelectedObject(jQuery(selectedElem), null, diffX*-1, diffY*-1);


				// var newElem;
				/*if (sourceImage.parentNode.getAttribute("clip-path") && sourceImage.parentNode.getAttribute("clip-path") != "") {//if clippath added
				 newElem = gtag.cloneNode(true);
				 var newclipPath = sourceSvg.find("clipPath")[0].cloneNode(true);
				 newclipPath.id = getNextId();
				 contentDefs.appendChild(newclipPath);
				 newElem.childNodes[0].setAttribute("clip-path", "url(#" + newclipPath.id + ")");
				 newElem.childNodes[1].id = getNextId();
				 newElem.setAttribute("pathStr", newElem.childNodes[1].getAttribute("d"));
				 } else {//if clippath is not applied
				 newElem = sourceImage.cloneNode(true);
				 //remove clippath if exist on canvas defs
				 if (selectedElem.tagName == "g") {//if clippath removed
				 var clipPathAttr = selectedElem.childNodes[0].getAttribute("clip-path");
				 if (clipPathAttr && clipPathAttr != "") {
				 var clipPathId = clipPathAttr.substring(5, clipPathAttr.indexOf(")")); //url(#svg_1_ClipPath)
				 var clippathInDefs = svgCanvas.getElem(clipPathId);
				 if (clippathInDefs && clippathInDefs.parentNode == defs) {
				 contentDefs.removeChild(clippathInDefs);
				 }
				 }
				 }
				 }*/

				var zoom = 1;

				//for filter
				var filterId = sourceImage.getAttribute("filter");
				if (filterId) {//filter is applied
					filterId = filterId.replace(/"/g, ''); // for ie
					filterId = filterId.substring(5, filterId.indexOf(")")); //url(#filterid)
					var filter = sourceSvg.find("filter")[0];
					var type = filter.getAttribute("type");
					var newFilter;
					if (color_filter_lib[type]) {//if filter is from color filters
						//getting filter from selected element and remove it if exist
						var oldFilterId;
						if (selectedElem.tagName == "g") {
							oldFilterId = selectedElem.childNodes[0].getAttribute("filter");
						} else {
							oldFilterId = selectedElem.getAttribute("filter");
						}
						//console.log("oldFilterId");
						//console.log(oldFilterId);
						if (oldFilterId) {
							oldFilterId = oldFilterId.replace(/"/g, ''); // for ie
							oldFilterId = oldFilterId.substring(5, oldFilterId.indexOf(")")); //url(#filterid)
							//if older filter is from color filter then remove it
							var id = oldFilterId.split("_")[1];//ie_twoColor_svg_2
							if (color_filter_lib[id]) {
								var oldfilter = svgCanvas.getElem(oldFilterId);
								if (oldfilter) {
									oldfilter.parentNode.removeChild(oldfilter);
								}
							}
						}
						//creating new one
						//var filter = sourceSvg.find("filter")[0];
						newFilter = filter.cloneNode(true);
						newFilter.id = getNextId();
						contentDefs.appendChild(newFilter);
					} else if (!getFilterFromCanvas(type)) {//if not exist on canvas then creating new one
						//var filter = sourceSvg.find("filter")[0];
						newFilter = filter.cloneNode(true);
						newFilter.id = getNextId();
						contentDefs.appendChild(newFilter);
					} else {
						newFilter = getFilterFromCanvas(type);
					}
					imgInSelectedElem.setAttribute("filter", "url(#" + newFilter.id + ")");
				}else{
					imgInSelectedElem.removeAttribute("filter");
				}
				//setting width and height of image
				/*
				 // setHref(imgInSelectedElem, getHref(sourceImage) );
				 imgInSelectedElem.setAttribute("width", imgInSelectedElem.getAttribute("width")*scale);
				 imgInSelectedElem.setAttribute("height", imgInSelectedElem.getAttribute("height")*scale);
				 //setting position
				 console.log("scale");
				 console.log(scale);
				 console.log("clipPathRatio");
				 console.log(clipPathRatio);
				 console.log(diffX);
				 console.log(diffY);
				 console.log(diffX*clipPathRatio);
				 console.log(diffY*clipPathRatio);
				 imgInSelectedElem.setAttribute("x", parseFloat(imgInSelectedElem.getAttribute("x")) + diffX*clipPathRatio);
				 imgInSelectedElem.setAttribute("y", parseFloat(imgInSelectedElem.getAttribute("y")) + diffY*clipPathRatio);
				 */

				if(selectedElem.hasAttribute("pathstr")){
					selectedElem.removeAttribute("pathstr");
				}
				selectedElem.setAttribute("pathstr", pathStr);

				if (btnClicked.getAttribute("textareaid")) {
					var textarea = jQuery("#" + btnClicked.getAttribute("textareaid"));
					checkOutOfBound(textarea, selectedElem);
				}

				jQuery("#imageEffect_window").hide();
			}


			function cancelBtnClickHandler(evt) {
				jQuery("#imageEffect_window").hide();
			}

			function startResizeObject(evt) {
				boolMousedown = true;
				if(isTouch()){
					evt = evt.originalEvent.touches[0];
				}
				startPosX = evt.pageX;
				startPosY = evt.pageY;
				if(isTouch()){
					jQuery(document).bind("touchend", stopMoveResizeDiv);
					jQuery(document).bind("touchmove", { objToMove: resizeDiv }, moveResizeDiv);
				}else{
					jQuery(document).bind("mouseup", stopMoveResizeDiv);
					jQuery(document).bind("mousemove", { objToMove: resizeDiv }, moveResizeDiv);
				}
				if (isTouch()) {
					jQuery(document).bind('touchmove', stopMovePage);
				}
				return false;
			}

			function moveResizeDiv(evt) {
				if (boolMousedown) {
					if(isTouch()){
						evt = evt.originalEvent.touches[0];
					}
					var xdiff = evt.pageX - startPosX;
					var ydiff = evt.pageY - startPosY;
					//console.log("xdiff");
					//console.log(xdiff);
					//console.log(ydiff);
					//console.log("mousemove");
					startPosX = evt.pageX;
					startPosY = evt.pageY;


					var gtag = sourceImage.parentNode.parentNode;
					var shape = jQuery(gtag).find("path")[0];
					var pathStr = shape.getAttribute('d');
					var bbox;
					if (pathStr && pathStr != "") {
						bbox = Raphael.pathBBox(pathStr);
						//bbox = svgedit.utilities.getBBox(shape);
					}

					var scaleX = ((bbox.width + xdiff) / bbox.width)
					var scaleY = ((bbox.height + ydiff) / bbox.height)
					if (checkbox[0].checked) {
						scaleX = scaleY = Math.min(scaleX, scaleY);
					}

					if (bbox.width * scaleX > 10 && bbox.height * scaleY > 10) {
						resizeDiv.css("top", parseFloat(resizeDiv.css("top")) + ydiff + "px");
						resizeDiv.css("left", parseFloat(resizeDiv.css("left")) + xdiff + "px");
						// var transform = "translate(" + bbox.x + "," + bbox.y + ") ";
						// transform += "scale(" + scaleX + "," + scaleY + ") ";
						// transform += "translate(" + bbox.x * -1 + "," + bbox.y * -1 + ")";
						// shape.setAttribute("transform", transform);
						// svgCanvas.recalculateDimensions(shape);
						var transform = 's' + scaleX + ',' + scaleY;
						pathStr = Raphael.transformPath(pathStr, transform);
						shape.setAttribute('d',pathStr);
						updateClipPath(sourceImage.parentNode);
					}

					if (checkbox[0].checked) {
						updateResizeDiv()
					}

					return false;
				}
			}

			function stopMoveResizeDiv(evt) {
				boolMousedown = false;
				jQuery(document).unbind("mouseup", stopMoveResizeDiv);
				jQuery(document).unbind("mousemove", moveResizeDiv);
				if (isTouch()) {
					jQuery(document).unbind("touchend", stopMoveResizeDiv);
					jQuery(document).unbind("touchmove", moveResizeDiv);
					jQuery(document).unbind('touchmove', stopMovePage);
				}
				updateClippathforIE(0);
			}

			function shapeClickHandler(event) {
				drawShapeOnCanvas(event.data.shapeType);
			}

			function filterClickHandler(event) {
				console.log(event);
				applyEffect(event.currentTarget, event.data.filterType);
			}

			function showPanel($selectedElem, $btnClicked) {

				// selectedElem = svgCanvas.getSelectedElems()[0];
				selectedElem = $selectedElem;
				btnClicked = $btnClicked;
				jQuery("#imageEffect_window").show();

				if(isTouch()){
					var body = document.body; // For Chrome, Safari and Opera
					var html = document.documentElement; // Firefox and IE places the overflow at the <html> level, unless else is specified. Therefore, we use the documentElement property for these two browsers

					body.scrollTop = 0;
					html.scrollTop = 0;
				}
				// strokeSlider.slider('value', 0);

				// jQuery("#ie_strokeSlider_label").val(0);
				// jQuery("#ie_stroke_style")[0].selectedIndex = 0;

				// var paint = new jQuery.jGraduate.Paint({ solidColor: "000000" });
				// ieStrokeColorPicker.setPaint(paint);

				var src, filterId, size;
				if (selectedElem.tagName == "g") {
					//move selected element to topleft of the canvas to get the path near to top left
					var image = selectedElem.childNodes[0].childNodes[0];
					// var zoom = svgCanvas.getZoom();
					//zoom = 1;
					var path = selectedElem.childNodes[1].cloneNode(true);

					//var layer1 = findNodeWithTitleName(svgCanvas.getContentElem().childNodes, "Layer 1");
					//
					//layer1.appendChild(path);
					//
					size = getSizeToFit(image);
					//
					//setObjectScale(path,size);
					path.setAttribute("d", selectedElem.getAttribute("pathstr"));

					// sourceImage.setAttribute("width", size[0]);
					// sourceImage.setAttribute("height", size[1]);
					sourceImage.setAttribute("width", size[0]);
					sourceImage.setAttribute("height", size[1]);

					src = getHref(image);
					var clipDef = sourceSvg.find("clipPath")[0];
					var gtag = sourceImage.parentNode;

					if (!clipDef) {
						var clipPathName = applyClipPath();
						//var clipPathId = "url(#"+clipPathName+")";
						//gtag.setAttribute('clip-path',clipPathId);
						clipDef = sourceSvg.find("clipPath")[0];
					}
					console.log("clipDef");
					console.log(clipDef);
					gtag.setAttribute('clip-path', "url(#" + clipDef.id + ")");

					jQuery(clipDef).empty();

					path.id = "ie_path";
					clipDef.appendChild(path);

					var pathInGtag = path.cloneNode(true);
					pathInGtag.setAttribute("stroke-width","1");
					if (gtag.parentNode.childNodes.length == 2)
						gtag.parentNode.removeChild(gtag.parentNode.childNodes[1]);
					gtag.parentNode.appendChild(pathInGtag);
					// jQuery(pathInGtag).on("mousedown", startMoveObject);
					if(isTouch()){
						jQuery(pathInGtag).on("touchstart", startMoveObject);
					}else{
						jQuery(pathInGtag).on("mousedown", startMoveObject);
					}
					// var strokewidth = pathInGtag.getAttribute("stroke-width") ? parseInt(pathInGtag.getAttribute("stroke-width")) : 1;
					// strokeSlider.slider('value', strokewidth);
					// jQuery("#ie_strokeSlider_label").val(strokewidth);
					// var paint = new jQuery.jGraduate.Paint({ solidColor: pathInGtag.getAttribute("stroke").substr(1) });
					// ieStrokeColorPicker.setPaint(paint);
					// var dashArray = pathInGtag.getAttribute("stroke-dasharray");
					// if (dashArray)
					// jQuery("#ie_stroke_style").val(dashArray);
					// else
					// jQuery("#ie_stroke_style")[0].selectedIndex = 0;
					sourceImage.setAttribute("origwidth", image.getAttribute("origwidth"));
					sourceImage.setAttribute("origheight", image.getAttribute("origheight"));
					if (image.getAttribute("templateSrc")) {
						sourceImage.setAttribute("templateSrc", image.getAttribute("templateSrc"));
						sourceImage.removeAttribute("templatesrc");
						if(image.hasAttribute("isadminuploaded")){
							sourceImage.setAttribute("isAdminUploaded", image.getAttribute("isadminuploaded"));
							sourceImage.removeAttribute("isadminuploaded");
						}else{
							sourceImage.setAttribute("isAdminUploaded", image.getAttribute("isAdminUploaded"));
						}
					}
					//gtag.setAttribute("lockTransform",selectedElem.childNodes[0].getAttribute("lockTransform"));
					//gtag.setAttribute("lockEdit",selectedElem.childNodes[0].getAttribute("lockEdit"));


					filterId = image.getAttribute("filter");

					updateClippathforIE(10);
					resizeDiv.show();

				} else {
					//removing clippath attribute
					var gtag = sourceImage.parentNode;
					gtag.removeAttribute("clip-path");

					resizeDiv.hide();
					if (gtag.parentNode.childNodes.length == 2) {
						gtag.parentNode.removeChild(gtag.parentNode.childNodes[1]);
					}

					src = getHref(selectedElem);
					sourceImage.setAttribute("origwidth", selectedElem.getAttribute("origwidth"));
					sourceImage.setAttribute("origheight", selectedElem.getAttribute("origheight"));
					if (selectedElem.getAttribute("templateSrc")) {
						sourceImage.setAttribute("templateSrc", selectedElem.getAttribute("templateSrc"));
						sourceImage.removeAttribute("templatesrc");
						if(selectedElem.hasAttribute("isadminuploaded")){
							sourceImage.setAttribute("isAdminUploaded", selectedElem.getAttribute("isadminuploaded"));
							sourceImage.removeAttribute("isadminuploaded");
						}else{
							sourceImage.setAttribute("isAdminUploaded", selectedElem.getAttribute("isAdminUploaded"));
						}
					}
					//sourceImage.setAttribute("lockTransform",selectedElem.getAttribute("lockTransform"));
					//sourceImage.setAttribute("lockEdit",selectedElem.getAttribute("lockEdit"));
					size = getSizeToFit(selectedElem);
					//console.log("size");
					//console.log(size);
					sourceImage.setAttribute("width", size[0]);
					sourceImage.setAttribute("height", size[1]);

					filterId = selectedElem.getAttribute("filter");
				}
				console.log("src = " + src);

				setHref(sourceImage, src);

				//setting svg at center
				// var top = (350 - size[1]) / 2;
				// var left = (500 - size[0]) / 2;
				var top = (150 - size[1]) / 2;
				var left = (300 - size[0]) / 2;
				jQuery(".imageeffectpreviw svg").css("top", top + "px");
				jQuery(".imageeffectpreviw svg").css("left", left + "px");


				//getting offset for scale and move
				var svgOffset = sourceSvg.offset();
				var containerOffset = jQuery("#imageEffect_window .global_popup_box").offset();
				offsetLeft = svgOffset.left - containerOffset.left;
				offsetTop = svgOffset.top - containerOffset.top;
				updateResizeDiv();
				//console.log("offsetLeft");
				//console.log(offsetLeft);
				//console.log(offsetTop);
				//for filters
				var filterContainer = jQuery("#ie_filters svg");

				filterContainer.find("image").each(function (i, child) {
					setHref(child, src);
				});
				console.log("filterId")
				console.log(filterId)
				if (filterId) {//filter is applied
					filterId = filterId.replace(/"/g, ''); // for ie
					filterId = filterId.substring(5, filterId.indexOf(")")); //url(#ie_filterid)
					var filter = document.getElementById(filterId);
					if (filter) {
						if (sourceSvg.find("filter").length) {
							sourceSvg.find("filter").remove();
						}
						var newFilter = filter.cloneNode(true);
						newFilter.id = getNextId();
						var defs = findDefs(sourceSvg[0]);
						defs.appendChild(newFilter);
						var type = newFilter.getAttribute("type");
						//console.log(newFilter.id);
						if (type.indexOf("twoColor") > -1 || type.indexOf("oneColor") > -1) {
							var funcR = jQuery(defs).find("feFuncR[type='table']")[0];
							var tableValuesR = funcR.getAttribute('tableValues').split(' ');

							var funcG = jQuery(defs).find("feFuncG[type='table']")[0];
							var tableValuesG = funcG.getAttribute('tableValues').split(' ');

							var funcB = jQuery(defs).find("feFuncB[type='table']")[0];
							var tableValuesB = funcB.getAttribute('tableValues').split(' ');

							if (type.indexOf("twoColor") > -1) {
								newFilter.id = "twoColor";
								jQuery("#radio_twoColor")[0].checked = true;
								var col1 = tableValuetoHex(tableValuesR[0]) + "" + tableValuetoHex(tableValuesG[0]) + "" + tableValuetoHex(tableValuesB[0]);
								var col2 = tableValuetoHex(tableValuesR[1]) + "" + tableValuetoHex(tableValuesG[1]) + "" + tableValuetoHex(tableValuesB[1]);
								//console.log("colorpicker");
								//console.log(col1);
								//console.log(col2);
								var paint = new jQuery.jGraduate.Paint({ solidColor: col1 });
								pickerAry[0].setPaint(paint);
								pickerAry[0].container.parent().show();
								paint = new jQuery.jGraduate.Paint({ solidColor: col2 });
								pickerAry[1].setPaint(paint);
								pickerAry[1].container.parent().show();
								sourceImage.setAttribute("colors", col1 + "," + col2);
							} else if (type.indexOf("oneColor") > -1) {
								newFilter.id = "oneColor";
								jQuery("#radio_oneColor")[0].checked = true;
								var col1 = tableValuetoHex(tableValuesR[0]) + "" + tableValuetoHex(tableValuesG[0]) + "" + tableValuetoHex(tableValuesB[0]);
								var paint = new jQuery.jGraduate.Paint({ solidColor: col1 });
								pickerAry[0].setPaint(paint);
								pickerAry[0].container.parent().show();
								pickerAry[1].container.parent().hide();
								sourceImage.setAttribute("colors", col1);
							}
						} else {
							// jQuery("#radio_original")[0].checked = true;
							// pickerAry[0].container.parent().hide();
							// pickerAry[1].container.parent().hide();
						}
						sourceImage.setAttribute("filter", "url(#" + newFilter.id + ")");
					}
				} else {
					sourceImage.removeAttribute("filter");
					sourceImage.setAttribute("colors", "");
					// jQuery("#radio_original")[0].checked = true;
					// pickerAry[0].container.parent().hide();
					// pickerAry[1].container.parent().hide();
				}

			}

			function tableValuetoHex(tableCalue) {
				var hex = Math.floor(tableCalue * 255).toString(16);
				return (hex.length == 1) ? "0" + hex : hex;
			}

			function setObjectScale(selectedElement, size) {
				var pathStr = selectedElement.getAttribute("d");
				var objBbox = svgedit.utilities.getBBox(selectedElement);
				//var objBbox = Raphael.pathBBox(pathStr);
				var zoom = svgCanvas.getZoom();
				//zoom = 1;
				var width = parseFloat(selectedElem.childNodes[0].getAttribute("width"));
				var height = parseFloat(selectedElem.childNodes[0].getAttribute("height"));
				var scale;
				//console.log("width = " + width);
				//console.log("height = " + height);
				//console.log("width = " + parseFloat(sourceSvg.attr("width")));
				//console.log("height = " + parseFloat(sourceSvg.attr("height")));
				if (width > height) {
					scale = size[0] / width;
				} else {
					scale = size[1] / height;
				}
				/*if(parseFloat(width) / parseFloat(height) <= (objBbox.width*zoom) / (objBbox.height*zoom)){
				 scale = (parseFloat(width)) / (objBbox.width*zoom);
				 }else{
				 scale = (parseFloat(height)) / (objBbox.height*zoom);
				 }*/
				//console.log("scale = " + scale);
				//console.log(objBbox);
				var posX = objBbox.x * zoom * scale;
				var posY = objBbox.y * zoom * scale;
				//console.log(posX);
				//console.log(posY);
				selectedElement.setAttribute("transform", "translate(" + posX * 1 + "," + posY * 1 + ") scale(" + scale + ") translate(" + posX * -1 + "," + posY * -1 + ")");
				//selectedElement.setAttribute("transform"," scale(" + scale + ")" );
				svgCanvas.recalculateDimensions(selectedElement);
			}

			function getSizeToFit(elem, width, height) {
				width = width || parseFloat(elem.getAttribute("width"));
				height = height || parseFloat(elem.getAttribute("height"))
				var availWidth = parseFloat(sourceSvg.attr("width"));
				var availHeight = parseFloat(sourceSvg.attr("height"));
				var ratio = width / height;
				if (width > height) {
					width = availWidth;
					height = width / ratio;
					if (height > availHeight) {
						height = availHeight;
						width = height * ratio;
					}
				} else {
					height = availHeight;
					width = height * ratio;
					if (width > availWidth) {
						width = availWidth;
						height = width / ratio;
					}
				}
				return [width, height];
			}

			function applyEffect(target, cur_filter_id) {
				if (sourceSvg.find("filter").length) {
					sourceSvg.find("filter").remove();
				}
				//console.log(target);
				var filter = jQuery(target).find("filter");
				//filter.setAttribute('id', cur_filter_id);
				var newFilter = filter[0].cloneNode(true);
				newFilter.id = getNextId();
				findDefs(sourceSvg[0]).appendChild(newFilter);
				sourceImage.setAttribute("filter", "url(#" + newFilter.id + ")");
			}

			function drawShapeOnCanvas(cur_shape_id) {
				var current_d = shape_lib[cur_shape_id];
				var cur_shape = document.createElementNS('http://www.w3.org/2000/svg', "path");
				cur_shape.setAttribute('d', current_d);
				cur_shape.setAttribute('id', "ie_path");
				cur_shape.setAttribute('opacity', 1);
				cur_shape.setAttribute('stroke', "#000");
				cur_shape.setAttribute('shapeType', cur_shape_id);
				//svgCanvas.recalculateDimensions(cur_shape);
				addShape(cur_shape);
			}

			var addShape = function (cur_shape) {

				//var bbox = cur_shape.getBoundingClientRect();
				var clipPathName = applyClipPath(cur_shape);
				//trace("clipPathName");
				//trace(clipPathName);
				var clipPathId = "url(#" + clipPathName + ")";
				var gtag = sourceImage.parentNode;
				//console.log(gtag.childNodes);
				gtag.setAttribute('clip-path', clipPathId);

				if (jQuery(gtag.parentNode).find("path").length) {
					jQuery(gtag.parentNode).find("path").remove();
				}
				var shapeTag = cur_shape.cloneNode(true);
				shapeTag.setAttribute("fill", "#000");
				shapeTag.setAttribute("fill-opacity", "0");
				var paint = ieStrokeColorPicker.paint;
				var clr = paint[paint.type];
				if (clr != "none" && clr.indexOf("#") == -1) {
					clr = "#" + clr;
				}
				var dashArray = jQuery("#ie_stroke_style")[0].options[jQuery("#ie_stroke_style")[0].selectedIndex].value;
				shapeTag.setAttribute("stroke", clr);
				shapeTag.setAttribute("stroke-width", strokeSlider.slider('value'));
				shapeTag.setAttribute("stroke-dasharray", dashArray);
				shapeTag.setAttribute("cursor", "move");
				gtag.parentNode.appendChild(shapeTag);

				if(isTouch()){
					jQuery(shapeTag).on("touchstart", startMoveObject);
				}else{
					jQuery(shapeTag).on("mousedown", startMoveObject);
				}

				updateResizeDiv();
				resizeDiv.show();
				updateClippathforIE(10);

			}

			function startMoveObject(evt) {
				//console.log("mousedown");
				boolMousedown = true;
				if(isTouch()){
					evt = evt.originalEvent.touches[0];
				}
				startPosX = evt.pageX;
				startPosY = evt.pageY;
				var gtag = sourceImage.parentNode.parentNode;
				if(isTouch()){
					jQuery(document).bind("touchend", stopMove);
					jQuery(document).bind("touchmove", { objToMove: jQuery(gtag).find("path")[0] }, moveObject);
				}else{
					jQuery(document).bind("mouseup", stopMove);
					jQuery(document).bind("mousemove", { objToMove: jQuery(gtag).find("path")[0] }, moveObject);
				}
				if (isTouch()) {
					jQuery(document).bind('touchmove', stopMovePage);
				}
				return false;
			}

			function stopMovePage(e) {
				e.preventDefault();
			}

			function moveObject(evt) {
				if (boolMousedown) {
					var xdiff;
					var ydiff;
					if(isTouch()){
						var touch = evt.originalEvent.touches[0];
						xdiff = touch.pageX - startPosX;
						ydiff = touch.pageY - startPosY;
					}else{
						xdiff = evt.pageX - startPosX;
						ydiff = evt.pageY - startPosY;
					}

					//console.log("xdiff");
					//console.log(xdiff);
					//console.log(ydiff);
					var path = evt.data.objToMove;
					//if(svgedit.browser.isIE()){
					//path.setAttribute("transform","translate("+xdiff+","+ydiff+")");
					//startPosX = evt.pageX;
					//startPosY = evt.pageY;
					//svgCanvas.recalculateDimensions(path);
					//updateClipPath(sourceImage.parentNode);
					//}else{
					var pathStr = path.getAttribute('d');
					transform = 't' + xdiff + ',' + ydiff;
					pathStr = Raphael.transformPath(pathStr, transform);
					path.setAttribute('d', pathStr);
					// svgCanvas.moveSelectedElements(xdiff / 5, ydiff / 5, false, [path]);
					if(isTouch()){
						var touch = evt.originalEvent.touches[0];
						startPosX = touch.pageX;
						startPosY = touch.pageY;
					}else{
						startPosX = evt.pageX;
						startPosY = evt.pageY;
					}

					updateClipPath(sourceImage.parentNode);
					//}
					updateResizeDiv();
					return false;
				}
			}

			function stopMove(evt) {
				//console.log("mouseup");
				boolMousedown = false;

				jQuery(document).unbind("mouseup", stopMove);
				jQuery(document).unbind("mousemove", moveObject);
				if (isTouch()) {
					jQuery(document).unbind("touchend", stopMove);
					jQuery(document).unbind("touchmove", moveObject);
					jQuery(document).unbind('touchmove', stopMovePage);
				}
				updateClippathforIE(0);
			}

			function updateResizeDiv() {
				var gtag = sourceImage.parentNode.parentNode;
				var shape = jQuery(gtag).find("path")[0];
				if (shape) {
					var pathStr = shape.getAttribute('d');
					var bbox;
					if (pathStr && pathStr != "") {
						bbox = Raphael.pathBBox(pathStr);
					}
					if (bbox) {
						console.log("bbox.width " + bbox.width);
						console.log("bbox.height " + bbox.height);
						console.log(bbox);
						console.log(shape.getBoundingClientRect());
						var boundingRect = shape.getBoundingClientRect();
						// if(isTouch()){
						// 	resizeDiv.css("left", (boundingRect.left + boundingRect.width) + "px");
						// 	resizeDiv.css("top", (boundingRect.top + boundingRect.height - 10) + "px");//10 is top of popup
						// }else{
						var containerOffset = jQuery("#imageEffect_window .global_popup_box").offset();
						var shapeOffset = jQuery(shape).offset();
						resizeDiv.css("left", (shapeOffset.left - containerOffset.left + boundingRect.width) + "px");
						resizeDiv.css("top", (shapeOffset.top - containerOffset.top + boundingRect.height ) + "px");//10 is top of popup
						// resizeDiv.css("left", (offsetLeft + bbox.x + bbox.width) + "px");
						// resizeDiv.css("top", (offsetTop + bbox.y + bbox.height) + "px");
						// }
					}
				}
			}

			/*var updateClipPath = function(elem){
			 var clipPath = sourceSvg.find("clipPath")[0];
			 var pathInClipPath = jQuery(clipPath).children()[0];
			 clipPath.replaceChild(jQuery(elem).children()[1].cloneNode(true),pathInClipPath);
			 }*/

			function updateClipPath(elem) {
				var clipPath = getClipPathElem(elem);
				if (clipPath) {
					var pathInClipPath = clipPath.childNodes[0];
					//clipPath.removeChild(pathInClipPath);
					//clipPath.appendChild(jQuery(elem).children()[1].cloneNode(true));
					clipPath.replaceChild(elem.parentNode.childNodes[1].cloneNode(true), pathInClipPath);
					clipPath.childNodes[0].setAttribute("id", "");
					//elem.removeAttribute("clip-path");
					//elem.setAttribute("clip-path","url(#"+clipPath.id+")");
				}
			}

			function getClipPathElem(elem) {
				var clipPathAttr = elem.getAttribute("clip-path");
				if (clipPathAttr && clipPathAttr != "") {
					clipPathAttr = clipPathAttr.replace(/"/g, ''); // for ie
					var clipPathId = clipPathAttr.substring(5, clipPathAttr.indexOf(")")); //url(#svg_1_ClipPath)
					return jQuery("#" + clipPathId)[0];
				}
				return null;
			}

			function applyClipPath(droppableShape) {
				var clipDef;
				if (sourceSvg.find("clipPath").length) {
					clipDef = sourceSvg.find("clipPath")[0];
					jQuery(clipDef).empty();
				} else {
					clipDef = document.createElementNS(SVGNS, "clipPath");
					clipDef.setAttribute('class', 'clippath');
					clipDef.setAttribute('id', "ie_clipRef" );
					findDefs(sourceSvg[0]).appendChild(clipDef);
				}
				if (droppableShape) {
					clipDef.appendChild(droppableShape);
				}
				return clipDef.id;
			}

			var showImageTab = function () {
				disableOther('addtimage');
				jQuery("addtimage").addClass('active');
				jQuery("#addtimage-panel").addClass('cbp-spmenu-open');
			}

			function showEditPanel() {
				jQuery("#common-panel").addClass('cbp-spmenu-open');
				if (toolType == "producttool") {
					if (isFront) {
						//jQuery('#common-panel').css("margin-top", "261px");
						jQuery('#common-panel').css("margin-top", "150px");
					} else {
						//jQuery('#common-panel').css("margin-top", "292px");
						jQuery('#common-panel').css("margin-top", "138px");
					}
				} else {
					//jQuery('#common-panel').css("margin-top", "183px");
					jQuery('#common-panel').css("margin-top", "77px");
				}
				//jQuery("#tool_fill,#border_box,#tool_Move").hide();
				jQuery("#tool_fill,#border_box").hide();
			}

			function updateClippathforIE(time) {
				if (isIE()) {
					var clipPath = getClipPathElem(sourceImage.parentNode);
					sourceImage.parentNode.removeAttribute("clip-path");
					setTimeout(updateClippathAttr, time, clipPath);
				}
			}

			function updateClippathAttr(clipPath, elem) {
				//var clipPath = getClipPathElem(sourceImage.parentNode);
				elem = elem || sourceImage.parentNode;
				elem.setAttribute("clip-path", 'url("#' + clipPath.id + '")');
			}
			function changeLabelsForLanguage() {
				//console.log("imageEffectbtn");
				//console.log(imageEffectbtn);
				// imageEffectbtn.text(uiStrings.elementLabel.imageEffectBtnCaption)

				jQuery("#ie_filters span")[0].innerHTML = langData[currentStore].filterlabel.original;
				jQuery("#ie_filters svg title")[0].innerHTML = svgEditor.langData[currentStore].filterlabel.original;
				var cnt = 1;//first one is original
				for (var type in filter_lib) {
					jQuery("#ie_filters span")[cnt].innerHTML = svgEditor.langData[currentStore].filterlabel[type];
					jQuery("#ie_filters svg title")[cnt].innerHTML = svgEditor.langData[currentStore].filterlabel[type];
					cnt++;
				}
				jQuery("#ie_colorChanger span")[0].innerHTML = svgEditor.langData[currentStore].filterlabel.original;
				cnt = 1;//first one is original
				for (var type in color_filter_lib) {
					jQuery("#ie_colorChanger span")[cnt].innerHTML = svgEditor.langData[currentStore].filterlabel[type];
					cnt++;
				}

				// updateBtn.text(svgEditor.langData[currentStore].elementLabel.UPDATE)
				// cancelBtn.text(svgEditor.langData[currentStore].elementLabel.CANCEL)
			}

			function updateStructure(elem) {
				if (elem.getAttribute("type") == "photobox" && elem.childNodes[0].tagName != "g") {
					var gtag = svgCanvas.addSvgElementFromJson({
						"element": "g",
						"attr": {
							"id": getNextId(),
							"style": "pointer-events:inherit"
						}
					});
					var image = elem.childNodes[0];
					elem.insertBefore(gtag, image);
					gtag.appendChild(image);
					var clippath = image.getAttribute("clip-path");
					image.removeAttribute("clip-path");
					gtag.setAttribute("clip-path", clippath);
				}
			}

			function updateAllPhotoboxStructure() {
				var photoLayernode = findNodeWithTitleName(jQuery("#svgcontent")[0].childNodes, "Layer 1");
				var rect;
				var returnObject = null;
				if (photoLayernode != null) {
					var i = photoLayernode.childNodes.length;
					while (i--) {
						var elem = photoLayernode.childNodes[i];
						if (elem.tagName == "g") {
							updateStructure(elem);
						}
					}
				}
			}

			function getHref(elem) {
				return elem.getAttributeNS(XLINKNS, "href");
			}

			function setHref(elem, val) {
				elem.setAttributeNS(XLINKNS, "xlink:href", val);
			}



			return {
				name: "imageEffect",
				init: init,
				findNodeWithTitleName: function (nodes, titleName) {
					var layer = findNodeWithTitleName(nodes,titleName);
					return layer;
				},
				imageLoaded: function (img, size) {
					var wid = 160;
					var hit = 90;
					if (toolType == "producttool") {
						wid = 160;
						hit = 160;
					}
					if (img.naturalWidth >= img.naturalHeight) {
						jQuery(img).css("width", wid + "px");
						jQuery(img).css("height", "auto");
						jQuery(img).css("margin-top", ((hit - img.height) / 2) + "px");
					} else {
						jQuery(img).css("height", hit + "px");
						jQuery(img).css("width", "auto");
						//img.height = 60;
						jQuery(img).css("margin-left", ((wid - img.width) / 2) + "px");
					}
				},
				getfilterFromClipBoard: function (filterId) {
					var cb = svgCanvas.clipBoard;
					var len = cb.length;
					var filter;
					while (len--) {
						var elem = cb[len];
						if (!elem) continue;
						//var svg = jQuery(elem).parentsUntil("svg")[0].parentNode;
						var svg = svgCanvas.clipBoardSide;
						if (svg.getElementById(filterId)) {
							filter = svg.getElementById(filterId);
						}
						break;
					}
					return filter;
				},
				showPanel: function (elem, btn) {
					showPanel(elem,btn);
				},
				langChanged: function () {
					changeLabelsForLanguage();
				},
				drawShapeOnCanvas: function (cur_shape_id) {
					drawShapeOnCanvas(cur_shape_id);
				},
				updateClipPath: function (elem) {
					updateClipPath(elem);
				},
				mouseDown: function () {
					if (svgedit.browser.isChrome()) {
						chromeMouseDownFlag = true;
					}
				},
				loadImageinPhotobox: function (photobox, imageSrc, orgWidth, orgHeight) {
					loadImageinPhotobox(photobox, imageSrc, orgWidth, orgHeight);
				},
				removeImage: function (selected) {
					removeImage(selected);
				},
				updateStructure: function (elem) {
					updateStructure(elem);
				},
				updateAllPhotoboxStructure: function () {
					updateAllPhotoboxStructure();
				},
				applyFilterOnImage: function (imageObject, imagesrc, origWidth, origHeight, filtertype, sideCnt) {
					applyFilterOnImage(imageObject, imagesrc, origWidth, origHeight, filtertype, sideCnt);
				}
			};
		})();
		specialChar = (function () {
			var container_window, container, clickfunc;

			function init() {
				var html = `<div id="specialChar_window" style="display:none;">
					<div class="global_overlay"></div>
					<div class="global_popup_box">
				<div class="toolbar_button" >
					<button onClick="this.parentNode.parentNode.parentNode.style.display = 'none'"  class="close-window-positoin">X</button>
				</div>
						<div class="popup-indent">
						<p class="new-heading pop-heading-line" id="specialCharPopupCaption">Special Character</p>
							<!--content goes here--> 
							<div id="specialChar-container" class="">
							
							</div>
						</div>
					</div>
				</div>`;
				var quickeditarea = document.getElementById("quickeditarea");
				if(quickeditarea != null){
					quickeditarea.parentNode.appendChild(jQuery(html)[0]);
				}

				container = document.getElementById("specialChar-container");
				container_window = document.getElementById("specialChar_window");
			}

			return {
				name: "specialChar",
				init:function(){
					init();
					this.createMap();
				},
				createMap: function(){
					for ( var i in this.categories ) {
						var catUL = document.createElement('ul');
						// catUL.setAttribute("class",'charMapCategory');
						catUL.setAttribute('data-category', i);
			
						for ( var c = 0; c < this.categories[i].length; ++c ) {
							cat =  this.categories[i][c];
			
							li = document.createElement('li');
							li.innerHTML = cat.char;
							if ( cat.entity ) {
								li.innerHTML = cat.entity;
							}
			
							li.setAttribute('data-char', li.innerHTML);
			
							if ( cat.name ) {
								li.setAttribute('data-name', cat.name);
								li.setAttribute('title', cat.name);
							}
			
							if ( cat.entity ) {
								li.setAttribute('data-entity', cat.entity);
							}
			
							li.addEventListener('click', this.onClick);
			
							catUL.appendChild(li);
						}
			
						container.appendChild(catUL);
					}
				},
				
				onClick: function(evt){
					console.log(evt.target.getAttribute('data-char'));
					if(clickfunc) clickfunc(evt.target)
					container_window.style.display = "none";
				},
	
				showMap: function(callBackFunc){
					clickfunc = callBackFunc;
					container_window.style.display = "block";
				},
				
				categories: {
					"Common":[
						{char: "™" }
						,{ entity: "&copy;", hex: "&#00A9;", name: "COPYRIGHT SIGN", char: "©" }
						,{ entity: "&reg;", hex: "&#00AE;", name: "REGISTERED SIGN", char: "®" }
						,{entity: "&dollar;", hex: "&#00A2;", name: "DOLLAR SIGN", char: "$" }
						,{entity: "&euro;", hex: "&#00A2;", name: "EURO SIGN", char: "€" }
						,{ entity: "&cent;", hex: "&#00A2;", name: "CENT SIGN", char: "¢" }
						,{ entity: "&pound;", hex: "&#00A3;", name: "POUND SIGN", char: "£" }
						,{ entity: "&curren;", hex: "&#00A4;", name: "CURRENCY SIGN", char: "¤" }
						,{ entity: "&yen;", hex: "&#00A5;", name: "YEN SIGN", char: "¥" }
						,{ entity: "&para;", hex: "&#00B6;", name: "PILCROW SIGN", char: "¶" }
						,{ entity: "&sect;", hex: "&#00A7;", name: "SECTION SIGN", char: "§" }
						,{ entity: "&uml;", hex: "&#00A8;", name: "DIAERESIS", char: "¨" }
				,{ entity: "&not;", hex: "&#00AC;", name: "NOT SIGN", char: "¬" }
				,{ entity: "&macr;", hex: "&#00AF;", name: "MACRON", char: "¯" }
				,{ entity: "&acute;", hex: "&#00B4;", name: "ACUTE ACCENT", char: "´" }
				,{ entity: "&micro;", hex: "&#00B5;", name: "MICRO SIGN", char: "µ" }
				,{ entity: "&middot;", hex: "&#00B7;", name: "MIDDLE DOT", char: "·" }
				,{ entity: "&cedil;", hex: "&#00B8;", name: "CEDILLA", char: "¸" }
				,{ entity: "&plusmn;", hex: "&#00B1;", name: "PLUS-MINUS SIGN", char: "±" }
				,{ entity: "&sup1;", hex: "&#00B9;", name: "SUPERSCRIPT ONE", char: "¹" }
				,{ entity: "&sup2;", hex: "&#00B2;", name: "SUPERSCRIPT TWO", char: "²" }
				,{ entity: "&sup3;", hex: "&#00B3;", name: "SUPERSCRIPT THREE", char: "³" }
				,{ entity: "&deg;", hex: "&#00B0;", name: "DEGREE SIGN", char: "°" }
				,{ entity: "&ordf;", hex: "&#00AA;", name: "FEMININE ORDINAL INDICATOR", char: "ª" }
				,{ entity: "&ordm;", hex: "&#00BA;", name: "MASCULINE ORDINAL INDICATOR", char: "º" }
				,{ entity: "&frac14;", hex: "&#00BC;", name: "VULGAR FRACTION ONE QUARTER", char: "¼" }
				,{ entity: "&frac12;", hex: "&#00BD;", name: "VULGAR FRACTION ONE HALF", char: "½" }
				,{ entity: "&frac34;", hex: "&#00BE;", name: "VULGAR FRACTION THREE QUARTERS", char: "¾" }
				,{ entity: "&iquest;", hex: "&#00BF;", name: "INVERTED QUESTION MARK", char: "¿" }
				,{ entity: "&iexcl;", hex: "&#00A1;", name: "INVERTED EXCLAMATION MARK", char: "¡" }
				,{ entity: "&brvbar;", hex: "&#00A6;", name: "BROKEN BAR", char: "¦" }
	
				,{ entity: "&laquo;", hex: "&#00AB;", name: "LEFT-POINTING DOUBLE ANGLE QUOTATION MARK", char: "«" }
				,{ entity: "&raquo;", hex: "&#00BB;", name: "RIGHT-POINTING DOUBLE ANGLE QUOTATION MARK", char: "»" }
	
				,{ hex: "&#25A0;", name: "BLACK SQUARE", char: "■" }
				,{ hex: "&#25A1;", name: "WHITE SQUARE", char: "□" }
				,{ hex: "&#25A2;", name: "WHITE SQUARE WITH ROUNDED CORNERS", char: "▢" }
				,{ hex: "&#25A3;", name: "WHITE SQUARE CONTAINING BLACK SMALL SQUARE", char: "▣" }
				,{ hex: "&#25A4;", name: "SQUARE WITH HORIZONTAL FILL", char: "▤" }
				,{ hex: "&#25A5;", name: "SQUARE WITH VERTICAL FILL", char: "▥" }
				,{ hex: "&#25A6;", name: "SQUARE WITH ORTHOGONAL CROSSHATCH FILL", char: "▦" }
				,{ hex: "&#25A7;", name: "SQUARE WITH UPPER LEFT TO LOWER RIGHT FILL", char: "▧" }
				,{ hex: "&#25A8;", name: "SQUARE WITH UPPER RIGHT TO LOWER LEFT FILL", char: "▨" }
				,{ hex: "&#25A9;", name: "SQUARE WITH DIAGONAL CROSSHATCH FILL", char: "▩" }
				,{ hex: "&#25AA;", name: "BLACK SMALL SQUARE", char: "▪" }
				,{ hex: "&#25AB;", name: "WHITE SMALL SQUARE", char: "▫" }
				,{ hex: "&#25AC;", name: "BLACK RECTANGLE", char: "▬" }
				,{ hex: "&#25AD;", name: "WHITE RECTANGLE", char: "▭" }
				,{ hex: "&#25AE;", name: "BLACK VERTICAL RECTANGLE", char: "▮" }
				,{ hex: "&#25AF;", name: "WHITE VERTICAL RECTANGLE", char: "▯" }
				,{ hex: "&#25B0;", name: "BLACK PARALLELOGRAM", char: "▰" }
				,{ hex: "&#25B1;", name: "WHITE PARALLELOGRAM", char: "▱" }
				,{ hex: "&#25B2;", name: "BLACK UP-POINTING TRIANGLE", char: "▲" }
				,{ hex: "&#25B3;", name: "WHITE UP-POINTING TRIANGLE", char: "△" }
				,{ hex: "&#25B4;", name: "BLACK UP-POINTING SMALL TRIANGLE", char: "▴" }
				,{ hex: "&#25B5;", name: "WHITE UP-POINTING SMALL TRIANGLE", char: "▵" }
				,{ hex: "&#25B6;", name: "BLACK RIGHT-POINTING TRIANGLE", char: "▶" }
				,{ hex: "&#25B7;", name: "WHITE RIGHT-POINTING TRIANGLE", char: "▷" }
				,{ hex: "&#25B8;", name: "BLACK RIGHT-POINTING SMALL TRIANGLE", char: "▸" }
				,{ hex: "&#25B9;", name: "WHITE RIGHT-POINTING SMALL TRIANGLE", char: "▹" }
				,{ hex: "&#25BA;", name: "BLACK RIGHT-POINTING POINTER", char: "►" }
				,{ hex: "&#25BB;", name: "WHITE RIGHT-POINTING POINTER", char: "▻" }
				,{ hex: "&#25BC;", name: "BLACK DOWN-POINTING TRIANGLE", char: "▼" }
				,{ hex: "&#25BD;", name: "WHITE DOWN-POINTING TRIANGLE", char: "▽" }
				,{ hex: "&#25BE;", name: "BLACK DOWN-POINTING SMALL TRIANGLE", char: "▾" }
				,{ hex: "&#25BF;", name: "WHITE DOWN-POINTING SMALL TRIANGLE", char: "▿" }
				,{ hex: "&#25C0;", name: "BLACK LEFT-POINTING TRIANGLE", char: "◀" }
				,{ hex: "&#25C1;", name: "WHITE LEFT-POINTING TRIANGLE", char: "◁" }
				,{ hex: "&#25C2;", name: "BLACK LEFT-POINTING SMALL TRIANGLE", char: "◂" }
				,{ hex: "&#25C3;", name: "WHITE LEFT-POINTING SMALL TRIANGLE", char: "◃" }
				,{ hex: "&#25C4;", name: "BLACK LEFT-POINTING POINTER", char: "◄" }
				,{ hex: "&#25C5;", name: "WHITE LEFT-POINTING POINTER", char: "◅" }
				,{ hex: "&#25C6;", name: "BLACK DIAMOND", char: "◆" }
				,{ hex: "&#25C7;", name: "WHITE DIAMOND", char: "◇" }
				,{ hex: "&#25C8;", name: "WHITE DIAMOND CONTAINING BLACK SMALL DIAMOND", char: "◈" }
				,{ hex: "&#25C9;", name: "FISHEYE", char: "◉" }
				,{ entity: "&loz;", hex: "&#25CA;", name: "LOZENGE", char: "◊" }
				,{ hex: "&#25CB;", name: "WHITE CIRCLE", char: "○" }
				,{ hex: "&#25CC;", name: "DOTTED CIRCLE", char: "◌" }
				,{ hex: "&#25CD;", name: "CIRCLE WITH VERTICAL FILL", char: "◍" }
				,{ hex: "&#25CE;", name: "BULLSEYE", char: "◎" }
				,{ hex: "&#25CF;", name: "BLACK CIRCLE", char: "●" }
				,{ hex: "&#25D0;", name: "CIRCLE WITH LEFT HALF BLACK", char: "◐" }
				,{ hex: "&#25D1;", name: "CIRCLE WITH RIGHT HALF BLACK", char: "◑" }
				,{ hex: "&#25D2;", name: "CIRCLE WITH LOWER HALF BLACK", char: "◒" }
				,{ hex: "&#25D3;", name: "CIRCLE WITH UPPER HALF BLACK", char: "◓" }
				,{ hex: "&#25D4;", name: "CIRCLE WITH UPPER RIGHT QUADRANT BLACK", char: "◔" }
				,{ hex: "&#25D5;", name: "CIRCLE WITH ALL BUT UPPER LEFT QUADRANT BLACK", char: "◕" }
				,{ hex: "&#25D6;", name: "LEFT HALF BLACK CIRCLE", char: "◖" }
				,{ hex: "&#25D7;", name: "RIGHT HALF BLACK CIRCLE", char: "◗" }
				,{ hex: "&#25D8;", name: "INVERSE BULLET", char: "◘" }
				,{ hex: "&#25D9;", name: "INVERSE WHITE CIRCLE", char: "◙" }
				,{ hex: "&#25DA;", name: "UPPER HALF INVERSE WHITE CIRCLE", char: "◚" }
				,{ hex: "&#25DB;", name: "LOWER HALF INVERSE WHITE CIRCLE", char: "◛" }
				,{ hex: "&#25DC;", name: "UPPER LEFT QUADRANT CIRCULAR ARC", char: "◜" }
				,{ hex: "&#25DD;", name: "UPPER RIGHT QUADRANT CIRCULAR ARC", char: "◝" }
				,{ hex: "&#25DE;", name: "LOWER RIGHT QUADRANT CIRCULAR ARC", char: "◞" }
				,{ hex: "&#25DF;", name: "LOWER LEFT QUADRANT CIRCULAR ARC", char: "◟" }
				,{ hex: "&#25E0;", name: "UPPER HALF CIRCLE", char: "◠" }
				,{ hex: "&#25E1;", name: "LOWER HALF CIRCLE", char: "◡" }
				,{ hex: "&#25E2;", name: "BLACK LOWER RIGHT TRIANGLE", char: "◢" }
				,{ hex: "&#25E3;", name: "BLACK LOWER LEFT TRIANGLE", char: "◣" }
				,{ hex: "&#25E4;", name: "BLACK UPPER LEFT TRIANGLE", char: "◤" }
				,{ hex: "&#25E5;", name: "BLACK UPPER RIGHT TRIANGLE", char: "◥" }
				,{ hex: "&#25E6;", name: "WHITE BULLET", char: "◦" }
				,{ hex: "&#25E7;", name: "SQUARE WITH LEFT HALF BLACK", char: "◧" }
				,{ hex: "&#25E8;", name: "SQUARE WITH RIGHT HALF BLACK", char: "◨" }
				,{ hex: "&#25E9;", name: "SQUARE WITH UPPER LEFT DIAGONAL HALF BLACK", char: "◩" }
				,{ hex: "&#25EA;", name: "SQUARE WITH LOWER RIGHT DIAGONAL HALF BLACK", char: "◪" }
				,{ hex: "&#25EB;", name: "WHITE SQUARE WITH VERTICAL BISECTING LINE", char: "◫" }
				,{ hex: "&#25EC;", name: "WHITE UP-POINTING TRIANGLE WITH DOT", char: "◬" }
				,{ hex: "&#25ED;", name: "UP-POINTING TRIANGLE WITH LEFT HALF BLACK", char: "◭" }
				,{ hex: "&#25EE;", name: "UP-POINTING TRIANGLE WITH RIGHT HALF BLACK", char: "◮" }
				,{ hex: "&#25EF;", name: "LARGE CIRCLE", char: "◯" }
					],
				}
				
			}
		})();
		multistyleText = (function () {
			var selection;
			var QlEditor;
			var defaultFontSize = 11;
			var defaultFontColor = "rgb(0, 0, 0)";
			var defaultFontFamily = "";
			var defaultFontIndex = 0;
			var defaultAlign = "start";
			var defaultStyle = "normal"
			var defaultWeight = "normal";
			var fontListAry; 
			var currentTextArea = null;
			var memLines = [];
			var memAligns = [];
			var selElem;
			var textmode = "";
			var textAreaBorderColor = "#CCCCCC";
			var advanceTxtBtn;
			var maxFontSize = 72;
			var hasBoldItalic = hasBold = hasItalic = false;
			alignMapping = {
				left: "start",
				center: "middle",
				right: "end"
			}
			var htmltoSVGAlign = {
				start: "left",
				middle: "center",
				end: "right"
			}
			var fontchangedByUser = false;
			 function init(){
				 var html = `<div id="multiStyleText_window" style="display:none;">
					<div class="global_overlay"></div>
					<div class="global_popup_box" >
				<div class="toolbar_button" >
					<button onClick="this.parentNode.parentNode.parentNode.style.display = 'none'" class="close-window-positoin">X</button>
				</div>
						<div class="popup-indent">
						<p class="new-heading pop-heading-line" id="editMultiStyleTextPopupCaption">Edit Text</p>
							<!--content goes here--> 
							<div id="formatting-container" class="ql-toolbar">
							<select title="Font" class="ql-font"></select>
					<select title="Size" class="ql-size"></select>
					<select title="Font" class="ql-heading"></select>
							<div class="ql-color"></div>
								
								<div class="size-bold-italic">
								<div class="left">
									<div title="Bold Text" class="tool_button ql-bold">B</div>
								</div>
								<div class="right">
									<div title="Italic Text" class="tool_button ql-italic">i</div>
								</div>
								</div>
								<div class="text-align">
								<div class="left">
									<div title="Align Left" id="ql-align-left" class="push_button_pressed"><span class="fa fa-align-left"></span></div>
								</div>
								<div class="left">
									<div title="Align Center" id="ql-align-center" class="tool_button"><span class="fa fa-align-center"></span></div>
								</div>
								<div class="right">
									<div title="Align Right" id="ql-align-right" class="tool_button"><span class="fa fa-align-right"></span></div>
								</div>
					</div>
					<div class="ql-formats">
						<div class="left" style="display:none;">
									<div title="Ordered List" id="ql-list-ordered" class="ql-list tool_button" value="ordered"><span class="fa fa-list-ol"></span></div>
								</div>
								<div class="left" style="display:none;">
									<div title="Bullet List" id="ql-list-bullet" class="ql-bullet tool_button" value="bullet"><span class="fa fa-list-ul"></span></div>
								</div>
								<div class="left">
									<div title="Special Character" id="ql-charmap" class="tool_button" ><span class="fa fa-venus"></span></div>
								</div>
					</div>
					<button id="btnAddUpdateText" class="dbtn btn-primary" title="Update">Update</button>
							</div>
							<div id="multieditor-container"></div>
							<div id="editor_bg">
								<div>
									<div class="left">
										<label id="editor_bg_caption" class="">Editor Background Color:</label>
									</div>
									<div class="left">
										<div title="White" id="ql-bg-white" class="push_button_pressed"><span></span></div>
									</div>
									<div class="left">
										<div title="Gray" id="ql-bg-gray" class="tool_button"><span></span></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>`;
				var quickeditarea = document.getElementById("quickeditarea");
				if(quickeditarea != null){
					quickeditarea.parentNode.appendChild(jQuery(html)[0]);
				}

				QlEditor = new Quill('#multieditor-container', {
					modules: {
						'toolbar': { container: '#formatting-container' },
						'link-tooltip': true,
						'image-tooltip': true
					}
				});

				function resetQlAlignButtons() {
					jQuery('#ql-align-left').removeClass('push_button_pressed').addClass('tool_button');
					jQuery('#ql-align-center').removeClass('push_button_pressed').addClass('tool_button');
					jQuery('#ql-align-right').removeClass('push_button_pressed').addClass('tool_button');
				}
				function setQlAlignButton(align) {
					resetQlAlignButtons();
					switch (align) {
						case 'left':
							jQuery('#ql-align-left').addClass('push_button_pressed').removeClass('tool_button');
							break;
						case 'center':
							jQuery('#ql-align-center').addClass('push_button_pressed').removeClass('tool_button');
							break;
						case 'right':
							jQuery('#ql-align-right').addClass('push_button_pressed').removeClass('tool_button');
							break;
					}
				}
				
				QlEditor.on('selection-change', function (range) {
					if (range) {
						fontchangedByUser = false;
						selection = QlEditor.getSelection();
						var sel;
						if (range.start == range.end) {
							if(range.start == 0){
								sel = QlEditor.getContents(range.start, range.end + 1);
							}else{
								sel = QlEditor.getContents(range.start - 1, range.end);
							}
						} else {
							sel = QlEditor.getContents(range.start, range.end);
						}
		
						//console.log("sel.ops.length :: ",sel.ops[0].insert.charAt(0)); 
		
						// if(QlEditor.getText(range.start, range.end) != ""){
						//     enabledButtons();
						// }else{
						//     disabledButtons();
						// }
		
						var clr = "rgb(0, 0, 0)";
						var align = "left";
						for (var i = 0; i < sel.ops.length; i++) {
							if (sel.ops[i].attributes) {
								if (sel.ops[i].attributes.color) {
									clr = sel.ops[i].attributes.color;
								}
								//if(sel.ops[i].attributes.align){
								//align = sel.ops[i].attributes.align;
								//}
							}
						}
						var leaf_format = QlEditor.editor.doc.findLeafAt(range.start)[0].formats;
						var line_format = QlEditor.editor.doc.findLineAt(range.start)[0].formats;
						if (line_format.align) {
							align = line_format.align;
						}
						jQuery(".ql-size option").removeAttr("Selected");
						if (!jQuery.isEmptyObject(leaf_format)) {
							if(!leaf_format.size) leaf_format.size = defaultFontSize+"px";
							var index = 0;
							for(var heading in headingData){
								var format = headingData[heading];
								if(format.size == leaf_format.size && format.bold == leaf_format.bold && format.italic == leaf_format.italic ){
									break;
								}
								index++;
							}
							if(index >= Object.keys(headingData).length){
								jQuery(".ql-heading")[0].selectedIndex = 0;
							}else{
								jQuery(".ql-heading")[0].selectedIndex = index+1;
							}
							if(leaf_format.size)
                    			jQuery(".ql-size option[value='"+leaf_format.size+"']").attr("Selected","Selected");
							// jQuery(".ql-size option").removeAttr("Selected");
							// jQuery(".ql-size option[value='"+leaf_format.size+"]").attr("Selected","Selected");
						}else{
							jQuery(".ql-font option").removeAttr("Selected");
							jQuery(".ql-font option[value='"+defaultFontFamily+"px']").attr("Selected","Selected");
							jQuery(".ql-size option[value='"+defaultFontSize+"px']").attr("Selected","Selected");
							jQuery(".ql-heading")[0].selectedIndex = 0;
						}
						console.log("leaf_format");
						console.log(leaf_format);
						clr = clr.substring(4, clr.length - 1);
						var rgb = clr.split(",");
						//console.log(rgbToHex(parseInt(rgb[0]),parseInt(rgb[1]),parseInt(rgb[2])));
						// var paint = new jQuery.jGraduate.Paint({ solidColor: rgbToHex(parseInt(rgb[0]), parseInt(rgb[1]), parseInt(rgb[2])) });
						var hexColor = rgbToHex(parseInt(rgb[0]), parseInt(rgb[1]), parseInt(rgb[2]));
						if(qlPicker != undefined){
							// qlPicker.setPaint(paint);
							if (colorPickerType == "Printable"|| colorPickerType.toString().toLowerCase() == "onecolor") {
								qlPicker.parent().find(".simpleColorDisplay").css("background-color", "#"+hexColor);
							}else{
								new jscolor(qlPicker.find("input")[0]).fromString(hexColor);
							}
							// qlPicker.attr("color", hexColor);
						}
						
						setQlAlignButton(align);
						
						//console.log("val");
						//console.log(jQuery(".ql-font").val());
						
						updateButtons(jQuery(".ql-font").val());
					} else {
						//console.log('Cursor not in the editor');
					}
				});

				 //font family
				 var i;
				 fontListAry = Object.keys(fontData); 
				 for (i = 0; i < fontListAry.length; i++) {
					 if(fontListAry[i] == "Bembo MT Pro"){
						 defaultFontIndex = i;
						 jQuery(".ql-font").append("<option value='" + fontListAry[i] + "' selected='selected' style='font-family:" + fontListAry[i] + ";' >" + fontListAry[i] + "</option>");
					 }else{
						jQuery(".ql-font").append("<option value='" + fontListAry[i] + "' style='font-family:" + fontListAry[i] + ";' >" + fontListAry[i] + "</option>");
					 }
				 }
				 defaultFontFamily = fontListAry[defaultFontIndex];
				 jQuery(".ql-container").css("font-family", fontListAry[defaultFontIndex]);

				 //font size
				 for (i = 8; i <= maxFontSize; i++) {
					if (i == defaultFontSize) {
						jQuery(".ql-size").append("<option selected='selected' value='" + i + "px'>" + i + "</option>");
					} else {
						jQuery(".ql-size").append("<option value='" + i + "px'>" + i + "</option>");
					}
				}
		
				updateButtons(fontListAry[defaultFontIndex]);
				jQuery(".ql-container").css("font-size", defaultFontSize);
		
				//font format
				var headingData = {
					"Body": {"size": "12px" },
					"Heading 1": {"size": "16px", "bold":true },
					"Heading 2": {"size": "14px", "bold":true },
					"Heading 3": {"size": "12px", "bold":true },
					"Heading 4": {"size": "11px", "bold":true, "italic":true },
					"Heading 5": {"size": "10px" , "bold":true},
					"Heading 6": {"size": "10px", "italic":true },
				}
				var headingAry = Object.keys(headingData); 
				jQuery(".ql-heading").append("<option value='' >Headings...</option>");
				for (i = 0; i < headingAry.length; i++) {
					var title = headingAry[i];
					var style = "font-size:" + headingData[title].size+"px;";
					if(headingData[title].bold){
						style += " font-weight: bold;";
					}
					if(headingData[title].italic){
						style += " font-style: italic";
					}
					jQuery(".ql-heading").append("<option value='" + title + "' style='"+style+"' >" + title + "</option>");
				}
		
				//font color
				// var cpString = '<div class="color_block_parent">';
				// cpString += '<div class="crossred"><svg viewBox="0 0 22 22" xmlns="http://www.w3.org/2000/svg" width="22" height="20" xmlns:xlink="http://www.w3.org/1999/xlink" class="svg_icon"><svg xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 22 22">		  <line fill="none" stroke="#d40000" id="svg_90" y2="22" x2="22" y1="0" x1="0"/>		  <line id="svg_92" fill="none" stroke="#d40000" y2="22" x2="0" y1="0" x1="22"/>	</svg></svg></div>';
				// cpString += '<div id="qlPicker" class="color_block"></div></div>';
				// jQuery('.ql-color').append(jQuery(cpString));
				var qlPicker = jQuery('<span id="qlPicker" title="'+langData[currentStore].changeColor+'" class="clrPicker" >&nbsp;</span>');
				jQuery('.ql-color').append(qlPicker);
				qlPicker.attr("color", "#000000");
				createPicker(qlPicker);
				// jQuery('#qlPicker').click(openColorPicker);
		
				//font alignment
				// jQuery('#qlPicker,#ql-align-left, #ql-align-center, #ql-align-right').mousedown(function (e) {
					// selection = QlEditor.getSelection();
				//     e.preventDefault();
				//     e.stopPropagation();
				// });
				jQuery('#ql-align-left').click(function (e) {
					e.preventDefault();
					e.stopPropagation();
		
					QlEditor.formatLine(selection.start, selection.end, 'align', "left");
					setQlAlignButton("left");
				});
				jQuery('#ql-align-center').click(function (e) {
					e.preventDefault();
					e.stopPropagation();
					// selection = QlEditor.getSelection();
					QlEditor.formatLine(selection.start, selection.end, 'align', "center");
					setQlAlignButton("center");
				});
				jQuery('#ql-align-right').click(function (e) {
					e.preventDefault();
					e.stopPropagation();
					// selection = QlEditor.getSelection();
					QlEditor.formatLine(selection.start, selection.end, 'align', "right");
					setQlAlignButton("right");
				});
				var canvas = jQuery("<div id='tempCanvas' style='display:none' ></div>").appendTo(jQuery("body"));
				paper = Raphael("tempCanvas", 500, 500);
		
				jQuery('#btnAddUpdateText').off('click');
				jQuery('#btnAddUpdateText').on('click', onBtnAddUpdateTextClick);
				
				jQuery(".ql-font").on("mousedown click", function(e){
					fontchangedByUser = true;
				});
				jQuery(".ql-font").on('change', function(e) {
					//console.log(e.target.value);
					
					/*var fontName = this.value;*/
					//if(fontName != ""){
					if(e.target.value != ""){
						//console.log("not null");
						console.log(fontchangedByUser);
						var fontName = e.target.value;
						if(localStorage.getItem(fontName)){
							Raphael.fonts[fontName] = JSON.parse(localStorage.getItem(fontName));
							updateButtons(fontName);
							if(fontchangedByUser){
								fontchangedByUser = false;
								if(!hasBold){
									QlEditor.formatText(selection.start, selection.end, "bold", false);
								}
								if(!hasItalic){
									QlEditor.formatText(selection.start, selection.end, "italic", false);
								}
							}
						}else{
							setTimeout(showLoader,500);
							console.log("font name :: "+fontData[fontName]);
							jQuery.getScript(fontData[fontName].jsFile)
							.done(function(){				
								setTimeout(hideLoader,500);
								try{
									localStorage.setItem(fontName, JSON.stringify(Raphael.fonts[fontName]) );
								}catch(e){
									localStorage.clear();
									localStorage.setItem(fontName, JSON.stringify(Raphael.fonts[fontName]) );
								}
								updateButtons(fontName);
								if(fontchangedByUser){
									fontchangedByUser = false;
									if(!hasBold){
										QlEditor.formatText(selection.start, selection.end, "bold", false);
									}
									if(!hasItalic){
										QlEditor.formatText(selection.start, selection.end, "italic", false);
									}
								}
							})
							.fail(function(){
								setTimeout(hideLoader,500);
								alert("Sorry!! this font is not available.");
							}); 
							
						}
					}
				});
				// jQuery(".ql-heading, #ql-list-ordered, #ql-list-bullet, #ql-charmap").on("mousedown", function(e){
					// selection = QlEditor.getSelection();
				// })
				jQuery(".ql-heading").on('change', function(e) {
					if(e.target.value != ""){
						//console.log("not null");
						if(selection){
							QlEditor.formatText(selection.start, selection.end, "bold", false);
							QlEditor.formatText(selection.start, selection.end, "italic", false);
							var format = headingData[e.target.value];
							for(var prop in format){
								QlEditor.formatText(selection.start, selection.end, prop, format[prop]);
							}
							QlEditor.setSelection(selection.start, selection.end);
						}
					}
				});
			   
				// jQuery("#ql-list-ordered").on('click', function(e) {
				//     if(selection){
				//         QlEditor.formatLine(selection.start, selection.end, "list", "true");
				//     }
				// });
		
				jQuery("#ql-charmap").on('click', function(e) {
					specialChar.showMap(addSpecialChar);
				});
				jQuery("#ql-bg-white, #ql-bg-gray").on('click', function(e) {
					jQuery("#ql-bg-white, #ql-bg-gray").removeClass("push_button_pressed tool_button");
					if(e.currentTarget.id == "ql-bg-white"){
						jQuery("#ql-bg-gray").addClass("tool_button");
						jQuery("#ql-bg-white").addClass("push_button_pressed");
						jQuery(".ql-container").css("background-color", "rgba(0, 0, 0, 0)");
					}else{
						jQuery("#ql-bg-white").addClass("tool_button");
						jQuery("#ql-bg-gray").addClass("push_button_pressed");
						jQuery(".ql-container").css("background-color", "#E2E2E2");
					}
				});
				jQuery("#multiStyleText_window .close-window-positoin").on("click", function(){
					selElem = null;
				})
				specialChar.init();
				if((colorPickerType == "Printable" && productData.onlySingleColor == 1) || colorPickerType.toString().toLowerCase() == "onecolor"){
					jQuery("#formatting-container .ql-color").css("pointer-events", "none");
				}
			 }

			 function addSpecialChar(li){
				var start = selection.start;
				var end = selection.end, contents;
				if(selection.start == selection.end){
					if(selection.start == 0){
						contents = QlEditor.getContents(start, start+1);
					}else{
						contents = QlEditor.getContents(start, start+1);
					}
					if(!contents.ops[0] || !contents.ops[0].attributes){
						contents = QlEditor.getContents(start-1, start);
						// end = start - 1;
					}
				}else{
					contents = QlEditor.getContents(start, end);
					end = start;
				}
				if(contents.ops[0] && contents.ops[0].attributes){
					if(contents.ops[0].attributes.list || contents.ops[0].attributes.bullet){
						QlEditor.insertText(end,li.getAttribute('data-char'));
					}else{
						QlEditor.insertText(end,li.getAttribute('data-char'), contents.ops[0].attributes);
					}
				}else{
					QlEditor.insertText(end,li.getAttribute('data-char'));
				}
			}

			 function editText(element, btn){
				if(element){
					currentTextArea = btn;
					selElem = element;
					qlPicker.setAttribute("elemid", element.id);
					jQuery('#btnAddUpdateText')[0].innerHTML = langData[currentStore].update;
					// jQuery("#editMultiStyleTextPopupCaption").text("Edit TextArea");
					QlEditor.setHTML("");
					//QlEditor.setHTML(selElem.getAttribute("desc").replace(/&quote;/g, '"'));
					//jQuery("#ql-editor-1").html(selElem.getAttribute("desc").replace(/&quote;/g, '"'));
					QlEditor.setHTML(selElem.getAttribute("desc").replace(/&quote;/g, '"'));
					jQuery('#multiStyleText_window').show();
					QlEditor.setSelection(0,QlEditor.getContents().length());
					jQuery(".ql-container").css("color", defaultFontColor);
				}
			}
		
			function setTextInArea(element){
				var rot = getRotationAngle(element);
				if(rot){
					setRotationAngle(0,element);
				}
				element.childNodes[0].childNodes[0].removeAttribute("transform");
				selElem = element;
				QlEditor.setHTML("");
				QlEditor.setHTML(selElem.getAttribute("desc").replace(/&quote;/g, '"'));
				onBtnAddUpdateTextClick();
				if(rot){
					svgCanvas.setRotationAngle(rot,element);
				}
			}

			 var row_max_width = 0, all_row_width = [], first_row_height = 0;
			function updateXY(elem){
				
				var selectedElem = elem || selElem;
				/*console.log("memLines");
				console.log(memLines);
				console.log("memAligns");
				console.log(memAligns);*/
				row_max_width = 0;
				var lineHeight = selectedElem.hasAttribute("lineHeight") ? selectedElem.hasAttribute("lineHeight") : selectedElem.hasAttribute("lineheight");
				var elementIndex;
				
				var initdx = 0;
				var initdy = 0;
				var dx;
				var dy;
				var k =  0;
				var l = 0;
				var blank_row_height = 0;
				all_row_width = [];

				for (var i = 0; i < memLines.length; i++) {
					var elements = memLines[i];
					
					dx = initdx;
					dy = initdy;
					var tempWidth  = 0;
					var tempHeight = [];
					
					var first_tspan_index_in_row = null;
					var row_width = 0;
					for (j = 0; j < elements.length; j++) {
						if(elements[j].text != ""){
							elementIndex = k;
							if(first_tspan_index_in_row == null){
								first_tspan_index_in_row = elementIndex;
							}
							k++;
							setTSpanBBox(selectedElem, elementIndex);
							//tempWidth.push(selectedElem[0].childNodes[elementIndex].getAttribute("bwidth"));
							tempHeight.push(selectedElem.childNodes[0].childNodes[0].childNodes[elementIndex].getAttribute("bheight"));
							var spanWidth = parseFloat(selectedElem.childNodes[0].childNodes[0].childNodes[elementIndex].getAttribute("bwidth"));
							row_width += spanWidth;
							tempWidth += spanWidth;
						}
					}
					
					
					all_row_width.push(row_width);
					if(row_max_width < row_width){
						row_max_width = row_width;
					}
					if(tempHeight.length){//something in the line
						initdy = Math.max.apply(null, tempHeight) ;
						initdx = -(tempWidth);
						if(i == 0){
							first_row_height = initdy;
						}
					}else{//nothing in the line
						blank_row_height += initdy;
					}
					
					if(first_tspan_index_in_row){//first tspan of all rows except first
						selectedElem.childNodes[0].childNodes[0].childNodes[first_tspan_index_in_row].setAttribute("dx",dx);
						selectedElem.childNodes[0].childNodes[0].childNodes[first_tspan_index_in_row].setAttribute("dy",(initdy + blank_row_height)*lineHeight);
						blank_row_height = 0;
					}else{
						if(first_tspan_index_in_row != null){//first tspan of first row
							selectedElem.childNodes[0].childNodes[0].childNodes[first_tspan_index_in_row].setAttribute("dx","0");
							selectedElem.childNodes[0].childNodes[0].childNodes[first_tspan_index_in_row].setAttribute("dy", blank_row_height * lineHeight);
							blank_row_height = 0;
						}else{

						}
					}
				}
				
				jQuery(selectedElem.childNodes[0].childNodes[0].childNodes).show();
			}

			function AlignRow(elem){
				var selectedElem = elem || selElem;
				var elementIndex = 0;
				var dx = 0 ;
				var dx_applied_to_earlier_tspan = 0;
				var areaWidth = Math.max(row_max_width, selectedElem.childNodes[1].getAttribute("width"));
				
				for (var i = 0; i < memLines.length; i++) {
						var first_tspan_index_in_row = null;
						var elements = memLines[i];
						for (j = 0; j < elements.length; j++) {
							if(elements[j].text != ""){
								if(first_tspan_index_in_row == null){
									var dist_to_move = 0;
									switch(memAligns[i]){
										case "middle":
											dist_to_move = (areaWidth - all_row_width[i]) / 2;
											break;
										case "end":
											dist_to_move = (areaWidth - all_row_width[i]);
											break;
									}
									var dx = parseFloat(selectedElem.childNodes[0].childNodes[0].childNodes[elementIndex].getAttribute("dx"));
									selectedElem.childNodes[0].childNodes[0].childNodes[elementIndex].setAttribute("dx", dx + dist_to_move - dx_applied_to_earlier_tspan);
									dx_applied_to_earlier_tspan = dist_to_move;
									first_tspan_index_in_row = elementIndex;
								}
								elementIndex++;
							}
						}
				}
			}

			function setTSpanBBox(elem, index){
				//console.log("index :: "+index);
        
				var selectedElem = elem || selElem || svgCanvas.getSelectedElems()[0];
				var curelem = jQuery(selectedElem.childNodes[0].childNodes[0].childNodes[index]);
				jQuery(selectedElem.childNodes[0].childNodes[0].childNodes).hide();
				curelem.show();
		
				//console.log("tspan id :: "+selectedElem[0].childNodes[index].id);
				/*jQuery(selectedElem[0].childNodes[index]).attr("by",selectedElem[0].getBoundingClientRect().y);
				jQuery(selectedElem[0].childNodes[index]).attr("bx",selectedElem[0].getBoundingClientRect().x);
				jQuery(selectedElem[0].childNodes[index]).attr("bwidth",selectedElem[0].getBoundingClientRect().width);
				jQuery(selectedElem[0].childNodes[index]).attr("bheight",selectedElem[0].getBoundingClientRect().height);*/
				// var bbox = selectedElem.childNodes[0].childNodes[0].getBBox();
				var bbox =  measuremyText('',curelem[0].getAttribute("font-size"),curelem[0].getAttribute("font-family"),curelem[0].getAttribute("font-weight"),curelem[0].getAttribute("font-style"),1);
				curelem.attr("by",bbox.y);
				curelem.attr("bx",bbox.x);
				curelem.attr("bwidth",curelem[0].getComputedTextLength());
				curelem.attr("bheight",bbox.height);
			}

			//for output
			function setBxBy(elem){
				var selectedElem = elem || selElem;
				var txt = selectedElem.childNodes[0].childNodes[0];
				var x = parseFloat(txt.getAttribute("x"));
				var y = parseFloat(txt.getAttribute("y"));
				var tspans = txt.childNodes;
				var totwidth = x;
				var prevx = 0;
				for(var cnt = 0; cnt < tspans.length; cnt++){
					var tspan = tspans[cnt];
					if(tspan.getAttribute("dx")){
						tspan.setAttribute("bx", totwidth + prevx + parseFloat(tspan.getAttribute("dx")));
						prevx = parseFloat(tspan.getAttribute("bx"));
						totwidth = parseFloat(tspan.getAttribute("bwidth"));
					}else{
						tspan.setAttribute("bx", totwidth + prevx);
						totwidth += parseFloat(tspan.getAttribute("bwidth"));
					}
				}
			}

			function decimalToHexString(number){
				if(number >= 0 && number <= 9) {
					return "0" + number
				}
				return number.toString(16);
			}

			function rgb2Hex(rgbvalue){
				var rgbHex = "#"
				var rgbCode = getRGBCode(rgbvalue);
				for(i = 0; i < rgbCode.length; i++) {
					rgbHex +=  decimalToHexString( parseInt(rgbCode[i]) )
				}
				return rgbHex;

			}

			function getRGBCode(rgbvalue){
				str = rgbvalue.substr(0,3)
				if(str == "rgb" || str == "RGB") {		
					initial = rgbvalue.indexOf('(');
					final = rgbvalue.indexOf(')');
					var rgbCode = rgbvalue.substr(initial+1,final-initial-1).split(',');
					if( (parseInt(rgbCode[0]) >= 0 && parseInt(rgbCode[0]) <= 255) && (parseInt(rgbCode[1]) >= 0 && parseInt(rgbCode[1]) <= 255) && (parseInt(rgbCode[2]) >= 0 && parseInt(rgbCode[2]) <= 255) ) {
						return rgbCode;
					}
					else{
						return [0,0,0];
					}
				}
			}


			
			function onBtnAddUpdateTextClick(e) {
				fontSize = defaultFontSize;
				fontFamily = defaultFontFamily;
				fontColor = defaultFontColor;
				align = defaultAlign;
				style = defaultStyle;
				//console.log('text-change', delta, source)
				var content = QlEditor.getContents();
				//console.log(content);
				var ops = content.ops;
				//var memLines = [];
				memLines = [];
				memAligns = [];
				var listNumber = 1;
				var bulletChar = "•";

				var currentLine = 0;
				//console.log("content");
				//console.log(QlEditor.getText().trim().toString());
				if(QlEditor.getText().trim() != ""){
					showLoader();
					memLines[currentLine] = new Array();
					
					for (var i = 0; i < ops.length; i++) {

						var info = ops[i];
						var attributes = info.attributes;

						//console.log("info");
						//console.log(info.insert);
						
						//console.log(attributes);

						if (info.insert == "\n") {
							if (attributes && attributes.hasOwnProperty("list")) {
								if(memLines[currentLine].length == 0) {
									memLines[currentLine].push({
										text: "",
										fontColor: defaultFontColor,
										fontSize: defaultFontSize,
										fontFamily: defaultFontFamily,
										style: defaultStyle,
										weight: defaultWeight
									});
								}
								memLines[currentLine][0].text = listNumber + ". " + memLines[currentLine][0].text;
								listNumber++;
							}else if (attributes && attributes.hasOwnProperty("bullet")) {
								if(memLines[currentLine].length == 0) {
									memLines[currentLine].push({
										text: "",
										fontColor: defaultFontColor,
										fontSize: defaultFontSize,
										fontFamily: defaultFontFamily,
										style: defaultStyle,
										weight: defaultWeight
									});
								}
								memLines[currentLine][0].text = bulletChar + " " + memLines[currentLine][0].text;
							}
							
								if (attributes && attributes.hasOwnProperty("align")) {
									align = alignMapping[attributes.align];
								} else {
									align = defaultAlign;
								}
								memAligns[currentLine] = align;
							
							currentLine++;
							memLines[currentLine] = new Array();
						} else {

							var lines = info.insert.split("\n");
							//console.log("lines");
							//console.log(lines);
							//remove last extra line
							if(i == ops.length -1 && lines[lines.length-1] == "" ) lines.pop();

							for (var j = 0; j < lines.length; j++) {

								if (attributes && attributes.hasOwnProperty("size")) {
									var size = attributes.size.split('px')[0];
									fontSize = Number(size);
								} else {
									fontSize = defaultFontSize;
								}

								if (attributes && attributes.hasOwnProperty("color")) {
									fontColor = attributes.color;
								} else {
									fontColor = defaultFontColor;
								}


								if (attributes && attributes.hasOwnProperty("font")) {
									fontFamily = attributes.font.replace(/^"+|"+$/gm, '');
								} else {
									fontFamily = defaultFontFamily;
								}

								if (attributes && attributes.hasOwnProperty("bold")) {
									weight = attributes.bold == true ? 'bold' : 'normal';
								} else {
									weight = defaultWeight;
								}

								if (attributes && attributes.hasOwnProperty("italic")) {
									style = attributes.italic == true ? 'italic' : 'normal';
								} else {
									style = defaultStyle;
								}

								// if (lines[j].length > 0) {
									memLines[currentLine].push({
										text: lines[j],
										fontColor: fontColor,
										fontSize: fontSize,
										fontFamily: fontFamily,
										style: style,
										weight: weight
									});

								// }

								if (lines.length > 1 && j < lines.length - 1) {

									if (attributes && attributes.hasOwnProperty("align")) {
										align = alignMapping[attributes.align];
									} else {
										align = defaultAlign;
									}
									//memLines[currentLine][0].align = align;
									memAligns[currentLine] = align;
									currentLine++;
									memLines[currentLine] = new Array();

								}

							}

						}
					}
					console.log("memLines", memLines);
					console.log("memAligns", memAligns);
					var desc = QlEditor.getHTML().replace(/"/g, "&quote;");
					var textareaWidth, textareaHeight;
					var textGroup, newText;
					if (selElem) {
						// var bbox = selElem.childNodes[0].childNodes[0].getBBox();
						textareaWidth = selElem.childNodes[1].getAttribute("width");
						textareaHeight = selElem.childNodes[1].getAttribute("height");
						selElem.setAttribute("desc", desc);
						textGroup = selElem;
						newText = selElem.childNodes[0].childNodes[0];
						newText.setAttribute("text-anchor", "start");//for converted textareas, if textarea has text-achor otherthan start
						jQuery(newText).empty();
						// textGroup.removeChild(textGroup.childNodes[1]);
					}else{
						textGroup = svgCanvas.addSvgElementFromJson({
							"element": "g",
							"attr": {
								"id": getNextId(),
								"type": "advance",
								// "text": jQuery("#text").val().replace(/"/g, "&quote;").replace(/\n/g, "@##@#@"),
								"align-vertical": "top",
								"font-weight": "normal",
								"font-style": "normal",
								"autofit": "false",
								"lineHeight": 1,
								"style": "pointer-events:inherit",
								"desc" : desc
							}
						});
						var textParent = svgCanvas.addSvgElementFromJson({
							"element": "g",
							"attr": {
								"id": getNextId(),
							}
						});
						//var zoom = svgCanvas.getZoom();
						newText = svgCanvas.addSvgElementFromJson({
							"element": "text",
							
							"attr": {
								"x": "0",//jQuery('#canvasBackground').attr('width') / 2 / zoom,
								"y": "0",//jQuery('#canvasBackground').attr('height') / 2 / zoom,
								"id": getNextId(),
								"fill": "#000000",
								"stroke-width": "0",
								"font-size": "24",
								"font-family": defaultFontFamily,
								//"text-anchor": "middle",
								
								"text-anchor": "start",
								//"align-vertical": "top",
								"xml:space": "preserve",
								"type": "advance",
								"opacity": "1",
							}
						});
						
						textParent.appendChild(newText);
						textGroup.appendChild(textParent);
					}
					//newText.textContent = "jinish";//jQuery("#text").val().split("\n").join(" ");
					

					
					for (var i = 0; i < memLines.length; i++) {
						var lineelements = memLines[i];

						//console.log("elements.length :: "+elements.length);
						var row_width = 0;
						for (j = 0; j < lineelements.length; j++) {
							if(lineelements[j].text != ""){
								//console.log("elements :: ",elements[j]);
								//var rgbClrCode = 0, 0, 0;
								var fontSize = lineelements[j].fontSize;
								var fontFamily = lineelements[j].fontFamily;
								var fontColor = rgb2Hex(lineelements[j].fontColor);
								var fontStyle = lineelements[j].style;
								var fontweight = lineelements[j].weight;
								
								var newTSpan = document.createElementNS(SVGNS, "tspan");
								var attr =  {
										"id": getNextId(),
										"fill": fontColor,
										"stroke-width": "0",
										"font-size": fontSize,
										"font-family": fontFamily,//first_font,
										//"text-anchor": "middle",
										//"text-anchor": "start",
										//"xml:space": "preserve",
										"opacity": "1",
										"font-weight":fontweight,
										"font-style":fontStyle
									}
									jQuery(newTSpan).attr(attr);
								//console.log("elements.text :: "+elements[j].text);
								newTSpan.textContent = lineelements[j].text;
								newText.appendChild(newTSpan);
								var elementIndex = newText.childNodes.length-1;
								setTSpanBBox(textGroup, elementIndex);
								//tempWidth.push(selectedElem[0].childNodes[elementIndex].getAttribute("bwidth"));
								var spanWidth = parseFloat(newTSpan.getAttribute("bwidth"));
								if(textareaWidth && row_width + spanWidth > textareaWidth){//wrap from here and rest of the elements to new line
									var words = lineelements[j].text.split(" ");
									if(words.length > 1){
										//add new elements in elements.
										var newelementwords = [];
										while(row_width + spanWidth > textareaWidth && words.length > 1){
											newelementwords.unshift(words.pop());
											newTSpan.textContent = words.join(" ") || " ";
											setTSpanBBox(textGroup, elementIndex);
											spanWidth = parseFloat(newTSpan.getAttribute("bwidth"));
										}
										//still if row_width + spanWidth is greater than textareawidddth then move the elemnt to new line
										//only if this line has more than one element and this is not first element.
										if(row_width + spanWidth > textareaWidth && lineelements.length > 1 && j > 0){
											memLines.splice(i+1,0,lineelements.splice(j,lineelements.length-j));
											memAligns.splice(i+1,0,memAligns[i]);
											newText.removeChild(newTSpan);
										}else{
											// add splitted words to new element 
											var newElement = JSON.parse(JSON.stringify(lineelements[j]));//clone current one and change its text value
											newElement.text = newelementwords.join(" ") || " ";
											lineelements[j].text = words.join(" ") || " ";
											lineelements.splice(j+1,0,newElement);
											//add new line in memlines.
											memLines.splice(i+1,0,lineelements.splice(j+1,lineelements.length-j));
											memAligns.splice(i+1,0,memAligns[i]);
										}
									}else{
										//if this line has only one element then let it be, do not wrap
										//but if more elements and this is not first element then move this element to new line
										if(lineelements.length > 1 && j > 0){
											memLines.splice(i+1,0,lineelements.splice(j,lineelements.length-j));
											memAligns.splice(i+1,0,memAligns[i]);
											newText.removeChild(newTSpan);
										}
									}
									break;
								}else{
									row_width += spanWidth;
								}
							}
						}
					}

					// setSizePosition(newText);

					jQuery("#multiStyleText_window").hide();
					updateXY(textGroup);
					updateArea(textGroup, textareaWidth, textareaHeight);
					
					var rect = textGroup.childNodes[1];
					newText.setAttribute("x",rect.getAttribute("x"));
					newText.setAttribute("y",parseInt(rect.getAttribute("y")) + first_row_height);
			
					var uniqeAlign = memAligns.concat();
					jQuery.unique(uniqeAlign);
					
					/*console.log("uniqeAlign");
					console.log(uniqeAlign);*/
					console.log("memAligns");
					console.log(memAligns);
					if(uniqeAlign.length == 0 || (uniqeAlign.length == 1 && uniqeAlign[0] == "start") ){

					}else{
						AlignRow(textGroup);
					}
					updateClipPath(textGroup.childNodes[0]);
					// removeGrips(textGroup);
					// svgCanvas.selectOnly([textGroup], true);
					// if(textGroup.getAttribute("lockTransform") == "true"){
					// 	svgCanvas.selectorManager.requestSelector(textGroup).showGrips(false);
					// }

					//adjust y position
					var textbbox = newText.getBBox();
					var diff = textbbox.y - parseInt(rect.getAttribute("y"));
					newText.setAttribute("y",parseInt(newText.getAttribute("y")) - diff);


					hideLoader();
					// if(textmode != "resize"){
					// 	svgCanvas.runExtensions("elementChanged", {
					// 		elems: [textGroup]
					// 	});
					// }
					if(currentTextArea){
						currentTextArea.value = getMultiTextContent(newText);
						currentTextArea = null;
					}
				}
				else{
					alert(svgEditor.uiStrings.notification.ADDTEXT_ALERT);
				}
				selElem = null;
			}

			function updateClipPath(elem){
				var clipPath = getClipPathElem(elem);
				if(clipPath){
					var pathInClipPath = clipPath.childNodes[0];
					clipPath.replaceChild(elem.parentNode.childNodes[1].cloneNode(true),pathInClipPath);
				}
			}
			
			function getClipPathElem(elem){
				var clipPathAttr = elem.getAttribute("clip-path");
				if(clipPathAttr && clipPathAttr != ""){
					clipPathAttr = clipPathAttr.replace(/"/g, ''); // for ie
					var clipPathId = clipPathAttr.substring(5,clipPathAttr.indexOf(")")); //url(#svg_1_ClipPath)
					return jQuery("clipPath#"+clipPathId)[0];
				}
				return null;
			}

			function updateArea(elem,width,height){
				console.log("updateArea");
				console.log("elem", elem);
				console.log("width", width);
				console.log("height", height);
				var textGroup = elem;
				// console.log("textGroup");
				// console.log(textGroup);
				var newBbox = textGroup.getBBox();
				width = width || newBbox.width;
				height = height || newBbox.height;
				// var size;
				// if(toolType=="producttool"){
				// 	size = [design_area_width,design_area_height];
				// }else{
				// 	var canSize = getSize();
				// 	size = [parseFloat(canSize[0])*typeMap_[baseUnit], parseFloat(canSize[1])*typeMap_[baseUnit]];
				// }

				// width = Math.min(width, parseFloat(size[0]));
				// height = Math.min(height, parseFloat(size[1]));
				if(textGroup.getElementsByTagName("rect").length){
					var rect = textGroup.getElementsByTagName("rect")[0];
					var oldwidth = rect.getAttribute("width");
					rect.setAttribute("width", width);
					rect.setAttribute("height", height);
					var text = textGroup.childNodes[0].childNodes[0];
					var align = text.getAttribute("text-anchor"); 
					if(text && align != "start" ){
						if(align == "middle")
							text.setAttribute("x", parseFloat(text.getAttribute("x")) + (width - oldwidth) / 2 );
						if(align == "end")
							text.setAttribute("x", parseFloat(text.getAttribute("x")) + (width - oldwidth)  );
					}
				}else{
					var rect = svgCanvas.addSvgElementFromJson({
						"element": "rect",
						"attr": {
							"id": getNextId(),
							"x": newBbox.x,
							"y": newBbox.y,
							"width": Math.min(width + 40, parseFloat(size[0])),
							"height": Math.min(height + 40, parseFloat(size[1])),
							"stroke": textAreaBorderColor,
							"stroke-width": "0.5",
							"stroke-dasharray": "5,5",
							"fill": "#000000",
							"fill-opacity": "0",
							"style": "pointer-events:inherit"
						}
					});
					textGroup.appendChild(rect);
					var clipRefId = applyClipPath(rect.cloneNode(true));
					textGroup.childNodes[0].setAttribute("clip-path","url(#"+clipRefId+")");
				}
				updateClipPath(textGroup.childNodes[0]);
			}
			
			function applyClipPath(droppableShape)
			{
				var clipDef = document.createElementNS('http://www.w3.org/2000/svg', "clipPath");
				clipDef.setAttribute('class','clippath');
				clipDef.setAttribute('id', getNextId());
				findDefs().appendChild(clipDef);
				clipDef.appendChild(droppableShape);
				return clipDef.id;
			}

			function updateButtons(fontName) {
				//console.log("fontName");
				//console.log(fontName);

				if(fontName == null) return;
				
				if(!Raphael.fonts) return;
				
				var font = Raphael.fonts[fontName];
				//console.log("font");
				//console.log(font);
				if(!font){
					//console.log("if fired***");
					if(localStorage.getItem(fontName)){
						Raphael.fonts[fontName] = JSON.parse(localStorage.getItem(fontName));
						updateButtons(fontName);
					}else{
						setTimeout(showLoader,500);
						console.log("font name :: "+fontData[fontName]);
						jQuery.getScript(fontData[fontName].jsFile)
						.done(function(){				
							setTimeout(hideLoader,500);
							try{
								localStorage.setItem(fontName, JSON.stringify(Raphael.fonts[fontName]) );
							}catch(e){
								localStorage.clear();
								localStorage.setItem(fontName, JSON.stringify(Raphael.fonts[fontName]) );
							}
							updateButtons(fontName);
						})
						.fail(function(){
							setTimeout(hideLoader,500);
							jQuery.alert("Sorry!! this font is not available.");
						}); 
						
					}
					return;
				}
				//console.log(font);
				hasBoldItalic = hasBold = hasItalic = false;
				for(var i=0; i< font.length; i++){
					if(font[i].face["font-weight"] == "700"){
						if(font[i].face["font-style"] == "italic" ){
							hasBoldItalic = true;
						}else{
							hasBold = true;
						}
					}else{
						if(font[i].face["font-style"] == "italic" ){
							hasItalic = true;
						}
					}
				}
				jQuery(".ql-bold").addClass("disabled");
				jQuery(".ql-italic").addClass("disabled");
				jQuery(".ql-bold").css("pointer-events", "none");
				jQuery(".ql-italic").css("pointer-events", "none");
				if(hasBoldItalic){
					jQuery(".ql-bold").removeClass("disabled");
					jQuery(".ql-bold").css("pointer-events", "inherit");
					jQuery(".ql-italic").removeClass("disabled");
					jQuery(".ql-italic").css("pointer-events", "inherit");
				}else if(hasBold){
					jQuery(".ql-bold").removeClass("disabled");
					jQuery(".ql-bold").css("pointer-events", "inherit");
				}else if(hasItalic){
					jQuery(".ql-italic").removeClass("disabled");
					jQuery(".ql-italic").css("pointer-events", "inherit");
				}
			}

			 function changeColor(color){
				 if(selection && selection.start != selection.end){
					 QlEditor.formatText(selection.start, selection.end, {
						 'color': color
					 });
				 }
			 }

			 function updateColor(elem, hexcode){
				var rgb = hexToRgb(hexcode);
				var desc = elem.getAttribute("desc").replace(/&quote;/g, '"');
				var clr = "rgb("+rgb.r+", "+rgb.g+", "+rgb.b+")";
				var tempDiv = document.createElement("div");
				//wrapping div
				tempDiv.innerHTML = desc;
				// var doc = svgedit.utilities.text2xml("<div>"+desc+ "</div>");
				var divs = jQuery(tempDiv).find("div");
				for(var divcnt=0; divcnt < divs.length; divcnt++){
					var div = divs[divcnt];
					for(cnt = 0; cnt < div.childNodes.length; cnt++){
						var chld = div.childNodes[cnt];
						if(chld.nodeName == "#text"){
							var span = document.createElement("SPAN");
							span.setAttribute("style", "color: "+ clr);
							span.innerText = chld.textContent;
							div.replaceChild(span, chld);
						}else{
							if(chld.nodeName == "SPAN"){
								jQuery(chld).css("color", clr);
							}
							var innerspans = jQuery(chld).find("span");
							if(innerspans.length){
								for(cntinner = 0; cntinner < innerspans.length; cntinner++){
									var innerspan = innerspans[cntinner];
									jQuery(innerspan).css("color", clr);
								}
							}
						}
					}
				}
				var str = tempDiv.innerHTML;
				str = str.replace(/"/g, "&quote;");
				//remove wrapped div
				// str = str.substr(5,str.length-11);
				elem.setAttribute("desc", str);
			}

			function convertTextAreaToMultiStyle(textarea, availableOnDOM){
				if(availableOnDOM){
					textarea.setAttribute("type", "advance");
					textarea.childNodes[0].childNodes[0].setAttribute("type", "advance");
					var txtlines = textarea.getAttribute("text").split("@##@#@");
					var rgb = hexToRgb(textarea.childNodes[0].childNodes[0].getAttribute("fill"));
					var clr = "rgb("+rgb.r+", "+rgb.g+", "+rgb.b+")";
					var align = htmltoSVGAlign[textarea.getAttribute("text-anchor")];
					var txt = "";
					for(var ln = 0; ln < txtlines.length; ln++){
						txt += "<div style=&quote;text-align: "+ align +";&quote;><span style=&quote;font-size: " +  textarea.getAttribute("font-size") + "px;&quote;><span style=&quote;color:"+ clr + ";&quote;><span style=&quote;font-family:" + textarea.getAttribute("font-family") + ";&quote;>" + txtlines[ln] + "</span></span></span></div>";
					}
					textarea.setAttribute("desc",  txt );
		
					selElem = textarea;
					QlEditor.setHTML("");
					QlEditor.setHTML(textarea.getAttribute("desc").replace(/&quote;/g, '"'));
					onBtnAddUpdateTextClick();
				}else{
					var clone = textarea.cloneNode(true);
					document.getElementById("svgcontent").appendChild(clone);
					//removing textarea after 500 ms so that fonts will be loaded
					setTimeout(function(){ clone.parentNode.removeChild(clone); }, 500);
					// selElem = clone;
					// QlEditor.setHTML("");
					// QlEditor.setHTML(clone.getAttribute("desc").replace(/&quote;/g, '"'));
					// onBtnAddUpdateTextClick();
					// textarea.parentNode.replaceChild(clone, textarea);
				}
				// textarea.removeChild("text");
			}
		
			function updateAllTextAreaToMultiStyle(pageNum){
				if(pageNum){
					convertSideTextAreaToMultiStyle(pageNum);
				}else{
					for(var i=0;i<quickEditSvg.length;i++){
						convertSideTextAreaToMultiStyle(i);
					}
				}
			}

			function getAllTextArea(elems){
				if (elems.length) {
					var selectedTexts = [];
					for (var i = 0; i < elems.length; i++) {
						if (elems[i].tagName == "g") {
							if (elems[i].getAttribute("type") == "textarea") {
								selectedTexts.push(elems[i]);
							} else {
								selectedTexts = selectedTexts.concat(jQuery.makeArray(jQuery(elems[i]).find('g[type="textarea"]')));
							}
						}
					}
					if (selectedTexts.length) {
						return selectedTexts;
					}
				}
				return null;
			}
		
			function convertSideTextAreaToMultiStyle(pageNum){
				var doc,content,parser,onDOM = false;
				if(currentSide == pageNum){
					doc = document.getElementById("svgcontent");;
					content = findNodeWithTitleName(doc.childNodes, "Layer 1");
					onDOM = true;
				}else{
					parser = new DOMParser();
					doc = parser.parseFromString(quickEditSvg[pageNum], "image/svg+xml");
					content = findNodeWithTitleName(doc.firstChild.childNodes, "Layer 1");
				}
				if(content){
					var allAreas= getAllTextArea(content.childNodes);
					if(allAreas){
						for (var nt = 0; nt < allAreas.length; nt++) {
							convertTextAreaToMultiStyle(allAreas[nt], onDOM);
						}
					}
				}
				// if(is_photo_product){
				// 	if(currentSide == pageNum){
				// 		content = findNodeWithTitleName(doc.childNodes, "Layer 2");
				// 	}else{
				// 		content = findNodeWithTitleName(doc.firstChild.childNodes, "Layer 2");
				// 	}
				// 	if(content){
				// 		var allAreas= getAllTextArea(content.childNodes);
				// 		if(allAreas){
				// 			for (var nt = 0; nt < allAreas.length; nt++) {
				// 				convertTextAreaToMultiStyle(allAreas[nt]);
				// 			}
				// 		}
				// 	}
				// }
				if(currentSide != pageNum){
					quickEditSvg[pageNum] = (new XMLSerializer()).serializeToString(doc);
				}
			}

			return {
				name: "multistyleText",
				init: init,
				showPanel: function (elem, btn) {
					showPanel(elem,btn);
				},
				editText: function (elem, btn) {
					editText(elem,btn);
				},
				changeColor: function (color) {
					changeColor(color);
				},
				updateColor: function (elem, hexcode) {
					updateColor(elem, hexcode);
				},
				setTextInArea: function (element) {
					setTextInArea(element);
				},
				updateAllTextAreaToMultiStyle: function (pageNum) {
					if(QlEditor && jQuery(document.getElementById("svgcontent")).find('g[type="textarea"]').length){
						showLoader();
						setTimeout(function(){
							updateAllTextAreaToMultiStyle(pageNum);
							updateRightPanel();
							hideLoader();
						},1500)
					}
				},
				setBxBy: function (element) {
					setBxBy(element);
				},
				changeDefaultcolor: function (hexCode) {
					var clr = hexToRgb(hexCode || hexCodeList[0]);
					defaultFontColor = "rgb("+ clr.r +", "+ clr.g +", "+ clr.b +")";
				},
			}
		})();
		var fontsToLoad = 0, fontLoaded = 0, sfFonts = [];
		jQuery(document).ready(function () {
			if (isQuickEdit) {
				// jQuery('body').trigger('processStart');
				noOfSides = parseInt(noOfSides);
				if (toolType == "producttool") {
					if(noOfSides > 1){
						var quickedit_changeside_button_holder = jQuery("#quickedit_changeside_button_holder");
						for(var cnt = 0 ; cnt < noOfSides; cnt++){
							var btn = jQuery('<button type="button" page="'+cnt+'" title="'+langData[currentStore][totalSides[cnt]]+'" class="button"><span><span>'+langData[currentStore][totalSides[cnt]]+'</span></span></button>');
							quickedit_changeside_button_holder.append(btn);
							btn.on("click", function(){
								quickedit_changeside_button_holder.children().each(function(i,btn){
									jQuery(btn).removeClass("active");
								})
								var page = this.getAttribute("page");
								jQuery(this).addClass("active");
								drawSvg(page);
							})
							if(cnt == 0){
								btn.addClass("active");
							}
						}
					}
					/* 
					jQuery('#changeSide span').html(langData[currentStore].view_back);
					jQuery("#changeSide").click(function () {
						if (jQuery("#changeSide").attr('page') == 0) {
							jQuery("#changeSide").attr('page', 1);
							jQuery('#changeSide span').html(langData[currentStore].view_front);
						} else {
							jQuery("#changeSide").attr('page', 0);
							jQuery('#changeSide span').html(langData[currentStore].view_back);
						}
						var side = jQuery("#changeSide").attr('page');
						drawSvg(side);
					});
					 */
					
					setTimeout(() => {
						jQuery(".swatch-attribute[attribute-id='"+productData.colorId+"'] .swatch-option").click(function(){
							var optionId = jQuery(this).attr("option-id");
							applyColorOnProduct(optionId);
							// console.log(cropper);
							// console.log(jquery-cropper);
						});
					}, 3000); 
					if(jQuery("#attribute"+productData.colorId).length){
						jQuery("#attribute"+productData.colorId).on("change", function(){
							// console.log(jQuery("#attribute"+productData.colorId).val());
							applyColorOnProduct(this.value);
						});
					}
				}else{
					var pageNav = jQuery("#pageNav");
					if(noOfSides > 1){
						pageNav.show();
					}else{
						pageNav.hide();
					}
					var firstPagebtn = jQuery("#pageNav .firstPage");
					firstPagebtn.on("click", function(evt){
						drawSvg(0);
						multistyleText.updateAllTextAreaToMultiStyle(0);
					})
					var prevPagebtn = jQuery("#pageNav .prevPage");
					prevPagebtn.on("click", function(evt){
						if(currentSide - 1 >= 0 ){
							drawSvg(currentSide-1);
							multistyleText.updateAllTextAreaToMultiStyle(currentSide);
						}
					})
					var nextPagebtn = jQuery("#pageNav .nextPage");
					nextPagebtn.on("click", function(evt){
						if(currentSide + 1 < noOfSides ){
							drawSvg(currentSide+1);
							multistyleText.updateAllTextAreaToMultiStyle(currentSide);
						}
					})
					var lastPagebtn = jQuery("#pageNav .lastPage");
					lastPagebtn.on("click", function(evt){
						drawSvg(noOfSides-1);
						multistyleText.updateAllTextAreaToMultiStyle(currentSide);
					})

					var gotoPageTxt = jQuery("#gotoPageTxt");
					// gotoPageTxt.on("input", function(evt){
					// 	drawSvg(noOfSides-1);
					// })
					
				}
				
				FontModule = (function () {
					var getFont = function () {
						jQuery.ajax({
							type: 'GET',
							url: fontUrl,
							cache: false,
							data: { 'formkey': formkey },
							success: function (response) {
								//fontData = jQuery.parseJSON(response);
								fontData = response;
								if (quickEditSvg) {
									//adding fonts
									var fontDiv = jQuery("<div id='font_container'></div>");
									var fontCon = jQuery("<Ul>");
									var closeBtn = jQuery('<div><i class="fa fa-times-circle" aria-hidden="true"></i></div>');
									closeBtn.on("click", closefontDiv)
									fontDiv.appendTo(quickPanel.parent());
									fontDiv.append(fontCon);
									fontDiv.append(closeBtn);
									jQuery.each(fontData, function(index,value){
										//jQuery.getScript(fontDirectory + value.jsFile);
										jQuery("head").append("<link>");
										var css = jQuery("head").children(":last");
										css.attr({
											rel:  "stylesheet",
											type: "text/css",
											href: value.cssFile
										});
										//if(value.jsFile){
										//jQuery.getScript(fontDirectory+value.jsFile);
										//}
										var li = jQuery('<li font-name="' + index + '"><a class="font-item" font-name="' + index + '" style="font-family:' + index + '">' + index + '</a></li>');
										li.on("click", changeFontFamily);
										fontCon.append(li);
									});
									multistyleText.init();
									if(quickeditWithJob){
										console.log("quickeditWithJob true.");
										setupCanvas();
									}else{
										//getting smart field data
										jQuery.ajax({
											type: 'GET',
											url: customerDetailUrl,
											cache: false,
											data: { 'formkey': formkey },
											success: function (response) {
												console.log("response from account details");
												console.log(response);
												sfFonts = [];
												if(response.status == "true"){
													for(var sideCnt = 0; sideCnt < quickEditSvg.length; sideCnt++){
														var parser = new DOMParser();
														var doc = parser.parseFromString(quickEditSvg[sideCnt], "image/svg+xml");
														var gTagLayer1 = findNodeWithTitleName(doc.firstChild.childNodes, "Layer 1");
														if(gTagLayer1){
															jQuery(gTagLayer1).find("g[type='text']").each(function(i, elem){
																if(elem.hasAttribute("sfid")){
																	sfFonts.push(elem.getAttribute("font-family"));
																}
															});
														}
													}
													sfFonts = jQuery.unique(sfFonts);
													console.log("sfFonts", sfFonts);
													fontsToLoad = sfFonts.length;
													preloadSFFonts(response);
												}else{
													console.log("user is not logged in.");
													setupCanvas();
												}
											},
											error: function(response){
												console.log("error getting user data.");
												setupCanvas();
											}
										});
									}
									

									

									
								}
								
								
							},
							error: function (response) {
								
								// hideLoader();
								//alert(uiStrings.notification.tryAgain);
							}
						});
					};
					return {
						init: getFont
					};
				})();


				unitInitialize();
				console.log("Margin");
				console.log(Margin);
				if (toolType == "web2print") {
					Margin.bleedMargin = bleedMargin;
					Margin.safeMargin = safeMargin;
				}
				jQuery.fn.jPicker.defaults.images.clientPath = baseUrl + 'js/html5/quickedit/images/';
				jQuery.fn.jPicker.defaults.localization = langData[currentStore].localization;
				FontModule.init();
				canvas = document.createElement('div');
				canvas.id = 'tempCanvas';
				canvas.setAttribute("style", "display:none");
				document.body.appendChild(canvas);

				paper = Raphael("tempCanvas", 500, 500);
				quickPanel = jQuery('#quickPanel');
				//console.log('sides');
				//console.log(sides);

				if (sides != null && sides.length > 0 && sides.id != '' && sides.id != undefined) {
					var sideOptionId = sides.id;

					if (Object.keys(sides.options).length > 1) {
						jQuery('#changeSide').show();
					}

					//sizeOptionOptionId = jQuery('select_'+sideOptionId).getValue();
					Event.observe('select_' + sideOptionId, 'change', function () {
						if (this.value) {
							if (sides.options[this.value].value == 1) {
								jQuery('#changeSide').hide();
								//drawSvg(0);
								if (currentSide == 1)
									jQuery('#changeSide').trigger('click');
							} else {
								jQuery('#changeSide').show();
							}
						}
					});
				} else if (parseInt(noOfSides)) {
					if (parseInt(noOfSides) > 1) {
						jQuery('#changeSide').show();
					}
				}

				imageEffect.init();
				cropImage.init();
				//zoom on mouse hover for non touch devices
				if(!isTouch()){
					var zoom_container = "#quickeditarea";
					if (toolType == "producttool") {
						zoom_container = "#product-image";
					}
					jQuery(document).on('mousemove', zoom_container, function(){
						var element = {
							width: jQuery(this).width(),
							height: jQuery(this).height()
						};
						
						var mouse = {
							x : event.pageX,
							y : event.pageY
						};
						
						var offset = jQuery(this).offset();
						
						var origin = {
							x: (offset.left+(element.width/2)),
							y: (offset.top+(element.height/2))
						};
						
						var trans = {
							left: (origin.x - mouse.x)/2,
							down: (origin.y - mouse.y)/2
						};
						
						var transform = ("scale(1.5,1.5) translateX("+ trans.left +"px) translateY("+ trans.down +"px)");
						
						jQuery(this).children("svg").css("transform", transform);
					
					});
					  
					jQuery(document).on('mouseleave', zoom_container, function(){
						jQuery(this).children("svg").css("transform", "none");
					});
				}
			}
		})
		
		function preloadSFImages(sfdata) {
			console.log("sfdata");
			console.log(sfdata);
			if(sfdata["corporate_logo"] && !sfdata["corporate_logo_object"]){
				jQuery('body').trigger('processStart');
				var img = new Image();
				img.onload = function(){
					jQuery('body').trigger('processStop');
					sfdata["corporate_logo_object"] = img;
					preloadSFImages(sfdata);
				}
				img.onerror = function(){
					jQuery('body').trigger('processStop');
					sfdata["corporate_logo_object"] = img;
					preloadSFImages(sfdata);
				}
				img.src = sfdata["corporate_logo"];
			}else if(sfdata["profile_image"] && !sfdata["profile_image_object"]){
				jQuery('body').trigger('processStart');
				var img = new Image();
				img.onload = function(){
					jQuery('body').trigger('processStop');
					sfdata["profile_image_object"] = img;
					preloadSFImages(sfdata);
				}
				img.onerror = function(){
					jQuery('body').trigger('processStop');
					sfdata["profile_image_object"] = img;
					preloadSFImages(sfdata);
				}
				img.src = sfdata["profile_image"];
			}else{
				replaceSmartFieldValues(sfdata);
				setupCanvas();
			}
		}
		function replaceSmartFieldValues(sfdata) {
			for(var sideCnt = 0; sideCnt < quickEditSvg.length; sideCnt++){
				var parser = new DOMParser();
				var doc = parser.parseFromString(quickEditSvg[sideCnt], "image/svg+xml");
				doc.childNodes[0].setAttribute("overflow", "hidden");
				doc.childNodes[0].id = 'svgcontent';
				var gTagLayer1 = findNodeWithTitleName(doc.firstChild.childNodes, "Layer 1");
				console.log("gTagLayer1");
				console.log(gTagLayer1);
				console.log("doc");
				console.log(doc);
				if(gTagLayer1){
					var svg = new XMLSerializer().serializeToString(doc);
					if (toolType == "web2print") {
						var quickeditarea = document.getElementById("quickeditarea");
						quickeditarea.innerHTML = svg;
					} else {
						if (jQuery("#svgroot").length) {
							jQuery("#svgroot").empty();//this if for IE
							jQuery("#svgroot").append(svg);//this if for IE
							// jQuery("#svgroot")[0].innerHTML = "svg";
						} else {
							var quickeditarea = document.getElementById("quickeditarea");
							svg = '<svg style="width: 400px; height: 485px; position: absolute; top: 0px; left: 0px;" overflow="visible" y="" x="" height="500" width="661" xmlns:xlink="http://www.w3.org/1999/xlink" xlinkns="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="svgroot">' + svg + '</svg>';
							quickeditarea.innerHTML = svg;
						}
					}
					var contentElem = document.getElementById("svgcontent");
					gTagLayer1 = findNodeWithTitleName(contentElem.childNodes, "Layer 1");
					var Objects = gTagLayer1.childNodes;
					updateSmartFieldValues(Objects, sideCnt, sfdata);
				}
				quickEditSvg[sideCnt] = getSvgString();
			}
		}

		function updateSmartFieldValues(Objects, sideCnt, sfdata, sfid_to_update, value_to_update) {
			for (var objCnt = Objects.length - 1; objCnt >= 0; objCnt--) {
				if (Objects[objCnt].hasAttribute("sfid")) {
					console.log("sfid");
					console.log(sfid);
					var sfid = Objects[objCnt].getAttribute("sfid");
					if(sfid_to_update && sfid != sfid_to_update){
						continue;
					}
					if(value_to_update && sfid == sfid_to_update){
						if (Objects[objCnt].getAttribute("type") == "photobox") {
							setImageinPhotobox(Objects[objCnt], value_to_update, true, sideCnt);
						}else if(Objects[objCnt].tagName == "image"){
							replaceImage(Objects[objCnt], value_to_update, true, sideCnt);
						}else if(Objects[objCnt].getAttribute("type") == "textarea"){
							// addUpdateText(null,Objects[objCnt],sfdata[sfid] );
						}else if(Objects[objCnt].getAttribute("type") == "text"){
							//addUpdateShapeText(null,Objects[objCnt],sfdata[sfid] );
						}else if(Objects[objCnt].tagName == "text"){
							addUpdateText(null,Objects[objCnt],value_to_update, false );
						}
					}else if(sfdata[sfid]){
						console.log(sfdata[sfid]);
						if (Objects[objCnt].getAttribute("type") == "photobox") {
							var img;
							if(sfid == "corporate_logo"){ 
								img = sfdata["corporate_logo_object"];
							}else if (sfid == "profile_image"){
								img = sfdata["profile_image_object"];
							}
							setImageinPhotobox(Objects[objCnt], img, true, sideCnt);
						}else if(Objects[objCnt].tagName == "image"){
							//for future scope
							var img;
							if(sfid == "corporate_logo"){ 
								img = sfdata["corporate_logo_object"];
							}else if (sfid == "profile_image"){
								img = sfdata["profile_image_object"];
							}
							replaceImage(Objects[objCnt], img, true, sideCnt);
						}else if(Objects[objCnt].getAttribute("type") == "textarea"){
							// addUpdateText(null,Objects[objCnt],sfdata[sfid] );
						}else if(Objects[objCnt].getAttribute("type") == "text"){
							//addUpdateShapeText(null,Objects[objCnt],sfdata[sfid] );
						}else if(Objects[objCnt].tagName == "text"){
							addUpdateText(null,Objects[objCnt],sfdata[sfid] );
						}
					}
				}
			}
		}

		function updateSmartFieldValuesOnCurrentPage(sfid_to_update, attribute_to_update, value_to_update, btn) {
			var parser = new DOMParser();
			var doc, gTagLayer1;
			var contentElem = document.getElementById("svgcontent");
			gTagLayer1 = findNodeWithTitleName(contentElem.childNodes, "Layer 1");
			
			if(gTagLayer1){
				var Objects = gTagLayer1.childNodes;
				for (var objCnt = Objects.length - 1; objCnt >= 0; objCnt--) {
					if (Objects[objCnt].hasAttribute("sfid")) {
						console.log("sfid");
						console.log(sfid);
						var sfid = Objects[objCnt].getAttribute("sfid");
						if(sfid == sfid_to_update){
							if(attribute_to_update == "move"){
								var elem = jQuery(Objects[objCnt]);
								if (elem.attr("type") == "photobox") {
									//moveSelectedObject(jQuery(elem[0].childNodes[0].childNodes[0]),evt.data.direction);
									moveSelectedObject(elem, value_to_update);
								} else {
									moveSelectedObject(elem, value_to_update);
								}
								if (btn.getAttribute("textareaid")) {
									var textarea = jQuery("#" + btn.getAttribute("textareaid"));
									checkOutOfBound(textarea, elem[0]);
								}
							}else if(attribute_to_update == "color"){
								var textElem = Objects[objCnt];
								if(textElem.getAttribute("type") == "textarea"){
									textElem.childNodes[0].childNodes[0].setAttribute("fill", value_to_update);
								}else if(textElem.tagName == "text"){
									textElem.setAttribute("fill", value_to_update);
								}else{
									if (textElem.childNodes.length) {
										for (var i = 0; i < textElem.childNodes.length; i++) {
											textElem.childNodes[i].setAttribute("fill", value_to_update);
										}
									}
								}
								textElem.setAttribute("fill", value_to_update);
							}else if(attribute_to_update == "font"){
								Objects[objCnt].setAttribute("font-family", value_to_update);
							}else if(attribute_to_update == "size"){
								var elem = jQuery(Objects[objCnt]);
								resizeSelectedObject(elem, value_to_update);
								if (btn.getAttribute("textareaid")) {
									var textarea = jQuery("#" + btn.getAttribute("textareaid"));
									checkOutOfBound(textarea, elem[0]);
								}
							}
						}
					}
				}
			}
		}

		function updateSmartFieldValue(sfid_to_update, value_to_update) {
			for(var sideCnt = 0; sideCnt < quickEditSvg.length; sideCnt++){
				var parser = new DOMParser();
				var doc, gTagLayer1;
				if(sideCnt == currentSide){
					var contentElem = document.getElementById("svgcontent");
					gTagLayer1 = findNodeWithTitleName(contentElem.childNodes, "Layer 1");
				}else{
					doc = parser.parseFromString(quickEditSvg[sideCnt], "image/svg+xml");
					gTagLayer1 = findNodeWithTitleName(doc.firstChild.childNodes, "Layer 1");
				}
				if(gTagLayer1){
					var Objects = gTagLayer1.childNodes;
					updateSmartFieldValues(Objects, sideCnt, null, sfid_to_update, value_to_update);
					if(sideCnt != currentSide){
						quickEditSvg[sideCnt] = new XMLSerializer().serializeToString(doc);
					}
				}
			}
		}

		function convertColor(){
			if((colorPickerType.toString().toLowerCase() == "printable" && productData.onlySingleColor == 1) || colorPickerType.toString().toLowerCase() == "onecolor"){
				var sidewise_colors = countAllSideDesignColors(true,true);//get sidewise colors in array
				var all_side_colors = [];
				for(var cnt = 0; cnt < sidewise_colors.length; cnt++){
					all_side_colors = all_side_colors.concat(sidewise_colors[cnt]);
				}
				all_side_colors = jQuery.unique(all_side_colors);
				var clr = all_side_colors[0];
				if(all_side_colors.length == 1 ){
					if(!alreadyInPrintable(all_side_colors[0], colorlist)){
						clr = colorlist[0].colorCode;
						convertDesignColor(true, colorlist[0].colorCode);
					}
				}else{
					clr = colorlist[0].colorCode;
					convertDesignColor(true, colorlist[0].colorCode);
				}
				updateRightPanel();
				return true;
			}
			return false;
		 }

		 var countAllSideDesignColors = function(allSide, uniqueColors) {
			uniqueColors = (uniqueColors == false) ? uniqueColors : true;
			if(allSide){
				var counter = [];
				for(var i=0; i<quickEditSvg.length; i++)
				{
					if(currentSide != i){
						var svgString = quickEditSvg.length[i];
						var colors = [];
						if(typeof svgString != "undefined" && svgString!= ''){
							var parser = new DOMParser();
							var doc = parser.parseFromString(svgString, "image/svg+xml");
							colors = countDesignColors(doc, uniqueColors);
						}
						counter[i] = colors;
					}
				}
				//var currentPage = jQuery("#current_page").val();		
				counter[currentSide] = countDesignColors(document.getElementById("svgcontent"), uniqueColors);
				return counter;
			}else{
				var colors = countDesignColors(document.getElementById("svgcontent"), uniqueColors);
				return colors;
			}
		};

		var countDesignColors = function(doc, uniqueColors){
		var colors = [];
		jQuery(doc).find(visElems).each(function(inc) {		
			// console.log("countSideElementColor");	
			// console.log(this);
			if(jQuery(this).attr('id') && jQuery(this).attr('id').indexOf("txtPathDef") <= -1 && jQuery(this).attr('id').indexOf("canvas_background") <= -1){
				if(jQuery(this).attr('fill') && jQuery(this).attr('fill') != 'none' && jQuery(this).attr('fill') != 'null' && jQuery(this).attr('fill') != '#ull' ){
					console.log(this.getAttribute('fill'));
					var color = this.getAttribute('fill');
					while(color.indexOf("#") == 0){
						color = color.substring(1);
					}
					color = strPad(color, 6, "0")
					colors.push(color);
				}
				if(jQuery(this).attr('stroke') && jQuery(this).attr('stroke') != 'none' && jQuery(this).attr('stroke') != 'null' && jQuery(this).attr('stroke') != '#ull'){
					// console.log(this.getAttribute('stroke'));
					var color = this.getAttribute('stroke');
					while(color.indexOf("#") == 0){
						color = color.substring(1);
					}
					color = strPad(color, 6, "0")
					colors.push(color);					
				}
				if(this.tagName == "image" && this.getAttribute("colors")){
					// console.log(this.getAttribute('colors'));
					var color = this.getAttribute('colors').split(",");
					for(var cnt=0; cnt < color.length; cnt++){
						while(color[cnt].indexOf("#") == 0){
							color[cnt] = color[cnt].substring(1);
						}
						color[cnt] = strPad(color[cnt], 6, "0")
						colors.push(color[cnt]);
					}
				}
			}
		});
		if(uniqueColors){
			colors = jQuery.unique(colors);
		}
		return colors;
	}

		var convertDesignColor = function(singleColor, colorCode) {
			for(var sideCnt = 0; sideCnt < quickEditSvg.length; sideCnt++){
				var svgString = quickEditSvg[sideCnt];
				if(typeof svgString != "undefined" && svgString!= ''){
					var parser = new DOMParser();
					var doc = parser.parseFromString(quickEditSvg[sideCnt], "image/svg+xml");
					convertSideElementColor(doc, singleColor, colorCode, sideCnt);
					quickEditSvg[sideCnt] = (new XMLSerializer()).serializeToString(doc);
				}
			}
			convertSideElementColor(document.getElementById("svgcontent"), singleColor, colorCode);
		};
		
		var convertSideElementColor = function(doc, singleColor, colorCode, sideCnt){		
			var i = 0;
			jQuery(doc).find(visElems).each(function(inc) {	
				console.log("convertSideElementColor");	
				console.log(this);
				if(jQuery(this).attr('id') && jQuery(this).attr('id').indexOf("txtPathDef") <= -1 && jQuery(this).attr('id').indexOf("canvas_background") <= -1){
					if(i >= colorlist.length){
						i = 0;
					}
					
					if(jQuery(this).attr('fill') && jQuery(this).attr('fill') != 'none' && jQuery(this).attr('fill') != 'null' && jQuery(this).attr('fill') != '#ull' && (!alreadyInPrintable(jQuery(this).attr('fill')) || singleColor )){
						// console.log(jQuery(this).attr('fill'));
						var clr = singleColor ? colorCode : colorlist[i].colorCode.toString();
						if(clr.indexOf("#") == -1){
							clr = "#" + clr;
						}
						// jQuery(this).attr('fill', clr);
						if((this.parentNode.getAttribute("type") == "text" || this.parentNode.getAttribute("type") == "textarea" || this.parentNode.getAttribute("type") == "advance") && this.tagName != "rect" && this.parentNode.firstChild != this){
							this.setAttribute("fill",this.parentNode.firstChild.getAttribute("fill"));
						}else{
							this.setAttribute('fill', clr);
							if(this.tagName == "text" && this.getAttribute("type") == "advance"){
								multistyleText.updateColor(this.parentNode.parentNode, clr);
							}
						}
						if(this.parentNode.getAttribute('type') == 'nameText'){
							var p = svgEditor.getPaint(clr, 100, "fill");
							svgEditor.paintBox.namefillColor.setPaint(p);
						}
						if(this.parentNode.getAttribute('type') == 'numberText'){
							var p = svgEditor.getPaint(clr, 100, "fill");
							svgEditor.paintBox.numberfillColor.setPaint(p);
						}
					}
					if(jQuery(this).attr('stroke') && jQuery(this).attr('stroke') != 'none' && jQuery(this).attr('stroke') != 'null' && jQuery(this).attr('stroke') != '#ull' && (!alreadyInPrintable(jQuery(this).attr('stroke')) || singleColor )){
						var clr = singleColor ? colorCode : colorlist[i].colorCode.toString();
						if(clr.indexOf("#") == -1){
							clr = "#" + clr;
						}
						// jQuery(this).attr('stroke', clr);
						if((this.parentNode.getAttribute("type") == "text" || this.parentNode.getAttribute("type") == "textarea" || this.parentNode.getAttribute("type") == "advance") && this.tagName != "rect" && this.parentNode.firstChild != this){
							this.setAttribute("stroke",this.parentNode.firstChild.getAttribute("stroke"));
						}else{
							this.setAttribute('stroke', clr);
						}							
					}
					if(singleColor && this.tagName == "image"){
						if(colorCode.indexOf("#") == 0){
							colorCode = colorCode.substr(1);
						}
						this.setAttribute("colors", colorCode);
						this.setAttribute("filtertype", "oneColor");
						imageEffect.applyFilterOnImage(this, getHref(this), this.getAttribute("origWidth"), this.getAttribute("origHeight"), "oneColor", sideCnt);
					}
				}
				i++;
			});
		}	
		function alreadyInPrintable(clipColor, colorList) {
			colorList = colorList || colorlist;
			var bool = false;
			while(clipColor.indexOf("#") == 0){
				clipColor = clipColor.substring(1);
			}
			clipColor = strPad(clipColor, 6, "0")
			clipColor = "#"+clipColor;
			jQuery(colorList).each(function () {
				var clr = this.colorCode.toString();
				if (this.colorCode.indexOf("#") == -1) {
					clr = "#" + clr;
				}
				//console.log(clr.toUpperCase());
				//console.log(clipColor.toUpperCase());
				if (clr.toUpperCase() == clipColor.toUpperCase()) {
					bool = true;
				}
			});
			return bool;
		}

		function getHref(elem) {
			return elem.getAttributeNS(XLINKNS, "href");
		}

		function setHref(elem, val) {
			console.log("sehref val");
			console.log(val);
			elem.setAttributeNS(XLINKNS, "xlink:href", val);
		}
		var webpath = 'http://127.0.0.1/dnb631/,https://onlineprintshop.remata.co.za/,https://www.onlineprintshop.remata.co.za/,https://www.onlineprintshop.remata.co.za/,http://onlineprintshop.remata.co.za/,https://printitza.co.za/,https://www.printitza.co.za/,http://printitza.co.za/,http://www.printitza.co.za/';
		
		function correctWebPath() {
			var bool = false;
			var multiDomain = webpath.split(",");
			for (var i = 0; i < multiDomain.length; i++) {
				if (window.location.href.indexOf(multiDomain[i]) != -1) {
					bool = true;
					webpath = multiDomain[i];
					break;
				}
			}
			return bool;
		}
		if(!correctWebPath()){
			alert("License key error!!!");
			return;
		}
		function replaceImgPath(str){
			var parser = new DOMParser();
			var doc = parser.parseFromString(str, "image/svg+xml");
			var images = doc.getElementsByTagName("image");
			var split_word = "pub/media";
			for(var cnt=0; cnt < images.length; cnt++){
				var image = images[cnt];
				var img_path = getHref(image);
				var templatesrc = image.getAttribute("templateSrc");
				var orighref = image.getAttribute("orighref");
				if(img_path && img_path.indexOf(webpath) == -1){
					console.log("img_path");
					console.log(img_path);
					var img_path_ary = img_path.split(split_word);
					img_path_ary[0] = webpath;
					img_path = img_path_ary.join(split_word);
					setHref(image,img_path);
					console.log(img_path);
				}
				if(templatesrc && templatesrc.indexOf(webpath) == -1){
					console.log("templatesrc");
					console.log(templatesrc);
					var template_path_ary = templatesrc.split(split_word);
					template_path_ary[0] = webpath;
					templatesrc = template_path_ary.join(split_word);
					image.setAttribute("templateSrc",templatesrc);
					image.removeAttribute("templatesrc");
					console.log(templatesrc);
				}
				if(orighref && orighref.indexOf(webpath) == -1){
					console.log("orighref");
					console.log(orighref);
					var template_path_ary = orighref.split(split_word);
					template_path_ary[0] = webpath;
					orighref = template_path_ary.join(split_word);
					image.setAttribute("orighref",orighref);
					console.log(orighref);
				}
			}
			str = (new XMLSerializer()).serializeToString(doc);
			return str;
		}

		function hasRequiredData(){
			var updatecurSide = updateCurrentSide;
			updateCurrentSide = true;
			getSvgString();
			updateCurrentSide = updatecurSide;
			var bool = false;
			var parser = new DOMParser();
			for(var i=0;i<quickEditSvg.length;i++){
				var doc = parser.parseFromString(quickEditSvg[i], "image/svg+xml");
				var content = findNodeWithTitleName(doc.firstChild.childNodes, "Layer 1");
				if(content){
					for (var nt = 0; nt < content.childNodes.length; nt++) {
						var elem = content.childNodes[nt];
						if(elem.getAttribute("isrequired") == "true" && elem.getAttribute("display") != "none" ){
							bool = true;
							//open page and select the item
							drawSvg(i);
							// if(toolType == "web2print"){
							// 	Navigation.gotoPage(i);
							// }else{
							// 	showSide(null, i);
							// }
							//get element from canvas
							// var elemOnCanvas = document.getElementById(elem.id);
							// svgCanvas.setSelectedElems([elemOnCanvas]);
							// svgCanvas.addToSelection([elem], true);
							//check if quick edit panel is not open then open it
							var formElement, msg;
							if(elem.tagName == "text" || elem.getAttribute("type") == "textarea" || elem.getAttribute("type") == "text" ){
								formElement = jQuery("textarea[elemid='"+elem.id+"']");
								msg = "Please change default text of highlighted element.";
							}else if(elem.tagName == "image" || elem.getAttribute("type") == "photobox"){
								formElement = jQuery("button[elemid='"+elem.id+"']");
								msg = "Please change default image of highlighted element.";
							}
							if(jQuery("#quickPanel").parent().css("display") == "none"){
								jQuery(".quickedit-filter-options-title").click();
							}
							highlightElementTillSelection(formElement);
							
							jQuery([document.documentElement, document.body]).animate({
								scrollTop: formElement.offset().top - 210
							  },500);
							setTimeout(alert,500, msg);
							// highlightElementTillSelection(elemOnCanvas);
							
							break;;
						}
					}
				}
				if(bool) break;
			}
			return bool;
		}
		var highlightTimer;
		function highlightElementTillSelection(elem) {
			highlightElement(jQuery(elem));
			// highlightTimer = setInterval(highlightElement, 1500, jQuery(elem));
			jQuery(document).on("mousedown",{elem:elem}, stophighlightElement );
		}
	
		function stophighlightElement(evt) {
			console.log("stop highlighting....");
			console.log(evt.data.elem);
			evt.data.elem.css("border-color","");
			clearInterval(highlightTimer);
			jQuery(document).off("mousedown", stophighlightElement );
		}
		function highlightElement(elem) {
			console.log("highlighting....");
			elem.css("border-color","red");
			// elem.animate({
			// 	opacity: 0,
			// }, 500, function() {
			// 	elem.animate({
			// 		opacity: 1,
			// 	}, 500, function() {
	
			// 	});
			// });
		}

		function getMultiTextContent(text) {
			var spans = jQuery(text).find('tspan');
			 if (spans.length == 0) {
			 spans = [text];
			 }
			 result = [];
			 jQuery.each(spans, function(k, span) {
			 result.push(span.textContent.replace("\n", " "));
			 });
			 return result.join(" ");
		 }
		
		function showLoader(){
			jQuery('body').trigger('processStart');
		}
		function hideLoader(){
			jQuery('body').trigger('processStop');
		}
		function componentToHex(c) {
			var hex = c.toString(16);
			return hex.length == 1 ? "0" + hex : hex;
		}
		function rgbToHex(r, g, b) {
			return componentToHex(r) + componentToHex(g) + componentToHex(b);
		}
		function hexToRgb(hex) {
			var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
			return result ? {
			  r: parseInt(result[1], 16),
			  g: parseInt(result[2], 16),
			  b: parseInt(result[3], 16)
			} : null;
		}

		function getTransformList(elem) {
			if (elem.transform) {
				return elem.transform.baseVal;
			}
			else if (elem.gradientTransform) {
				return elem.gradientTransform.baseVal;
			}
			else if (elem.patternTransform) {
				return elem.patternTransform.baseVal;
			}
			return null;
		};

		function getRotationAngle(elem, to_rad) {
			var selected = elem;
			// find the rotation transform (if any) and set it
			var tlist = getTransformList(selected);
			if(!tlist) {//console.log("no tlist");
			return 0;} // <svg> elements have no tlist
			var N = tlist.numberOfItems;
			for (var i = 0; i < N; ++i) {
				var xform = tlist.getItem(i);
				if (xform.type == 4) {
					return to_rad ? xform.angle * Math.PI / 180.0 : xform.angle;
				}
			}
			return 0.0;
		}

		setRotationAngle = function(val, element) {
			// ensure val is the proper type
			val = parseFloat(val);
			var elem = element;
			var bbox = elem.getBBox();
			var cx = bbox.x+bbox.width/2, cy = bbox.y+bbox.height/2;
			var tlist = getTransformList(elem);
			
			// only remove the real rotational transform if present (i.e. at index=0)
			if (tlist.numberOfItems > 0) {
				var xform = tlist.getItem(0);
				if (xform.type == 4) {
					tlist.removeItem(0);
				}
			}
			// find R_nc and insert it
			if (val != 0) {
				var center = transformPoint(cx,cy,transformListToTransform(tlist).matrix);
				var R_nc = svg.createSVGTransform();
				R_nc.setRotate(val, center.x, center.y);
				if(tlist.numberOfItems) {
					tlist.insertItemBefore(R_nc, 0);
				} else {
					tlist.appendItem(R_nc);
				}
			}
			else if (tlist.numberOfItems == 0) {
				elem.removeAttribute("transform");
			}
		}

		function transformPoint(x, y, m) {
			return { x: m.a * x + m.c * y + m.e, y: m.b * x + m.d * y + m.f};
		}

		function transformListToTransform(tlist, min, max) {
			if(tlist == null) {
				// Or should tlist = null have been prevented before this?
				return svg.createSVGTransformFromMatrix(svg.createSVGMatrix());
			}
			var min = min == undefined ? 0 : min;
			var max = max == undefined ? (tlist.numberOfItems-1) : max;
			min = parseInt(min);
			max = parseInt(max);
			if (min > max) { var temp = max; max = min; min = temp; }
			var m = svg.createSVGMatrix();
			for (var i = min; i <= max; ++i) {
				// if our indices are out of range, just use a harmless identity matrix
				var mtom = (i >= 0 && i < tlist.numberOfItems ? 
								tlist.getItem(i).matrix :
								svg.createSVGMatrix());
				m = svgedit.math.matrixMultiply(m, mtom);
			}
			return svg.createSVGTransformFromMatrix(m);
		};

		var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');

		function measuremyText(text,fontsize,fontname,isbold,isitalic,lineheight,letterspacing) 
		{
			text = text.replace(/\s/g,"tg");
			if(text =='')
			{
				text = 'tg';
			}
			var lDiv = document.createElement('div');
			document.body.appendChild(lDiv);
			lDiv.style.fontSize = "" + fontsize + "px";
			lDiv.style.position = "absolute";
			lDiv.style.left = -1000;
			lDiv.style.top = -1000;
			lDiv.style.fontFamily=fontname;
			if(isbold)
			{
				lDiv.style.fontWeight="bold";
			}
			if(isitalic)
			{
				lDiv.style.fontStyle = "italic";
			}
			//jQuery(lDiv).css("line-height","normal"); // necessary to get correct text height
			var templineheight = lineheight || 1;
			var templetterspacing = letterspacing || 0;
			jQuery(lDiv).css("line-height",templineheight); // necessary to get correct text height
			jQuery(lDiv).css("letter-spacing",templetterspacing + "px"); // necessary to get correct letter spacing
			lDiv.innerHTML = text;
			var tempwidth = lDiv.clientWidth ;
			var tempheight = lDiv.clientHeight;
			if(navigator.userAgent.indexOf("Chrome") != -1 ) //hack from chrome
			{
				tempheight = tempheight+1;
			}
			/* if(isitalic)
			{
				tempwidth = tempwidth + ((tempwidth/text.length)*.5);
			} */
			var lResult = {
				width: tempwidth,
				height: tempheight
			};
			document.body.removeChild(lDiv);
			lDiv = null;
			return lResult;
		}

		return {
			getSvgString: function () {
				getSvgString();
			},
			hasRequiredData: function () {
				return hasRequiredData();
			},
			drawcartSvg: function (sideCnt) {
				var sideCnt;
				drawSvg(sideCnt, false);
				console.log('ADD too From Quick eidt Files');
			}};

	});