<?php

// $contents = \JShrink\Minifier::minify($contents, array('flaggedComments' => false));

	ini_set('display_errors', 1);
	if (isset($_REQUEST["type"])) {
		$type = $_REQUEST["type"];
		if ($type == "canvas" || $type == "product" || $type == "both") {
			$srcPath = "../DO_NOT_UPLOAD/web2print-src.js";
			if ($type == "product") {
				$srcPath = "../DO_NOT_UPLOAD/product-src.js";
			}
			$myfile = fopen($srcPath, "r") or die("Unable to open file!");
						
			while(!feof($myfile)) {
				$str = trim(fgets($myfile));
			  if (strpos($str,"webpath") && (strpos($str,"//") || intval(strpos($str,"//")) > 0) ){
				  $webpath = substr($str,strpos($str,"http"),strlen($str)-2-strpos($str,"http"));
				  break;
			  }
			}
			fclose($myfile);
			
			$folder = getcwd();
			// $folder = substr($folder,0,strpos($folder,'dnb\\'));
			// $folder = $folder."dnb";
			// if(!is_dir($folder))
			// 	mkdir($folder , 0777);

			$domains = explode(",",$webpath);	
			$myfile = fopen($folder.DIRECTORY_SEPARATOR."sessionKeys.txt", "w");
			for( $i=0; $i < count($domains); $i++){
				$txt = (string)md5($domains[$i]);
				//$txt = PHP_EOL.$txt;
				fwrite($myfile, $txt."#**#");
			}
			fclose($myfile);
			//chmod($folder."/sessionKeys.txt", 0644);
			
			if($type == "canvas" || $type == "both"){
				//minify required files
				$myfile = fopen("Gruntfile.js", "w") or die("Unable to open Gruntfile.js!");
				$txt = "module.exports = function(grunt) {
			  grunt.initConfig({
				  uglify: {
					options: {
					   mangle: {
						except: ['jQuery']
					  }
					},
					my_target: {
					  files: {
						'built.canvas.min.js': ['../DO_NOT_UPLOAD/pathseg.js','../DO_NOT_UPLOAD/touch.js','../DO_NOT_UPLOAD/browser.js', '../DO_NOT_UPLOAD/svgtransformlist.js', '../DO_NOT_UPLOAD/math.js', '../DO_NOT_UPLOAD/units.js', '../DO_NOT_UPLOAD/svgutils.js', '../DO_NOT_UPLOAD/sanitize.js', '../DO_NOT_UPLOAD/select.js', '../DO_NOT_UPLOAD/history.js', '../DO_NOT_UPLOAD/draw.js', '../DO_NOT_UPLOAD/path.js','../DO_NOT_UPLOAD/md5-min.js', '../DO_NOT_UPLOAD/Margin.js', '../DO_NOT_UPLOAD/ImportTemplate.js', '../DO_NOT_UPLOAD/dnb/DNBBaseObject.js', '../DO_NOT_UPLOAD/helper.js', '../DO_NOT_UPLOAD/web2print-src.js','../DO_NOT_UPLOAD/svgcanvas.js', '../DO_NOT_UPLOAD/svg-editor.js','../DO_NOT_UPLOAD/locale/locale.js','../DO_NOT_UPLOAD/contextmenu.js','../DO_NOT_UPLOAD/../DO_NOT_UPLOAD/extensions/ext-navigation.js', '../DO_NOT_UPLOAD/extensions/ext-web2print.js', '../DO_NOT_UPLOAD/font_jsapi.js','../DO_NOT_UPLOAD/font-selector.js','../DO_NOT_UPLOAD/raphael.js','../DO_NOT_UPLOAD/plupload/moxie.js', '../DO_NOT_UPLOAD/plupload/plupload.dev.js', '../DO_NOT_UPLOAD/plupload/jquery.ui.plupload.js', '../DO_NOT_UPLOAD/plupload/imageuploader.js','../DO_NOT_UPLOAD/jquery.jscrollpane.js','../DO_NOT_UPLOAD/jquery.mCustomScrollbar.min.js','../DO_NOT_UPLOAD/jquery.simple-color.js','../DO_NOT_UPLOAD/quill.js','../DO_NOT_UPLOAD/extensions/ext-grid.js','../DO_NOT_UPLOAD/extensions/ext-multiColor.js','../DO_NOT_UPLOAD/extensions/ext-objectPanel.js','../DO_NOT_UPLOAD/extensions/ext-objectLock.js','../DO_NOT_UPLOAD/extensions/ext-LayerPanel.js','../DO_NOT_UPLOAD/extensions/ext-textArea.js','../DO_NOT_UPLOAD/extensions/ext-TextQuickEdit.js','../DO_NOT_UPLOAD/extensions/ext-textShape.js','../DO_NOT_UPLOAD/extensions/ext-recentColors.js','../DO_NOT_UPLOAD/extensions/ext-pickDesignColor.js','../DO_NOT_UPLOAD/extensions/ext-fliptools.js','../DO_NOT_UPLOAD/extensions/ext-distribute.js','../DO_NOT_UPLOAD/extensions/ext-imageEffect.js','../DO_NOT_UPLOAD/extensions/ext-dropbox.js','../DO_NOT_UPLOAD/extensions/ext-vdp.js','../DO_NOT_UPLOAD/extensions/ext-smartfields.js','../DO_NOT_UPLOAD/extensions/ext-undoRedo.js','../DO_NOT_UPLOAD/extensions/ext-corporate.js','../DO_NOT_UPLOAD/dependentoptions.js','../DO_NOT_UPLOAD/extensions/ext-ShowObjectSize.js','../DO_NOT_UPLOAD/extensions/ext-guidelines.js','../DO_NOT_UPLOAD/extensions/ext-getquote.js','../DO_NOT_UPLOAD/extensions/ext-threedpreview.js','../DO_NOT_UPLOAD/extensions/ext-mobileapp.js','../DO_NOT_UPLOAD/extensions/ext-pages.js','../DO_NOT_UPLOAD/extensions/ext-marketPlace.js','../DO_NOT_UPLOAD/extensions/ext-background.js','../DO_NOT_UPLOAD/extensions/ext-photoBook.js','../DO_NOT_UPLOAD/extensions/ext-multiStyleText.js','../DO_NOT_UPLOAD/extensions/ext-specialChar.js']
					  }
					}
				  }
				});
				grunt.loadNpmTasks('grunt-contrib-uglify');
			};";
				fwrite($myfile, $txt);
				$output = shell_exec('grunt uglify 2>&1');
				echo "<pre>";
				print_r($output);
			}
			
			if ($type == "product" || $type == "both") {
								
				//minify required files
				
				$myfile = fopen("Gruntfile.js", "w") or die("Unable to open Gruntfile.js!");
				$txt = "module.exports = function(grunt) {
			  grunt.initConfig({
				  uglify: {
					options: {
					   mangle: {
						except: ['jQuery']
					  }
					},
					my_target: {
					  files: {
						'built.product.min.js': ['../DO_NOT_UPLOAD/pathseg.js','../DO_NOT_UPLOAD/touch.js','../DO_NOT_UPLOAD/browser.js', '../DO_NOT_UPLOAD/svgtransformlist.js', '../DO_NOT_UPLOAD/math.js', '../DO_NOT_UPLOAD/units.js', '../DO_NOT_UPLOAD/svgutils.js', '../DO_NOT_UPLOAD/sanitize.js', '../DO_NOT_UPLOAD/select.js', '../DO_NOT_UPLOAD/history.js', '../DO_NOT_UPLOAD/draw.js', '../DO_NOT_UPLOAD/path.js','../DO_NOT_UPLOAD/md5-min.js', '../DO_NOT_UPLOAD/ImportTemplate.js', '../DO_NOT_UPLOAD/dnb/DNBBaseObject.js', '../DO_NOT_UPLOAD/helper.js',  '../DO_NOT_UPLOAD/ProductModule.js', '../DO_NOT_UPLOAD/product-src.js','../DO_NOT_UPLOAD/svgcanvas.js', '../DO_NOT_UPLOAD/svg-editor.js','../DO_NOT_UPLOAD/locale/locale.js','../DO_NOT_UPLOAD/contextmenu.js', '../DO_NOT_UPLOAD/extensions/ext-product.js', '../DO_NOT_UPLOAD/font_jsapi.js','../DO_NOT_UPLOAD/font-selector.js','../DO_NOT_UPLOAD/raphael.js','../DO_NOT_UPLOAD/plupload/moxie.js', '../DO_NOT_UPLOAD/plupload/plupload.dev.js', '../DO_NOT_UPLOAD/plupload/jquery.ui.plupload.js', '../DO_NOT_UPLOAD/plupload/imageuploader.js','../DO_NOT_UPLOAD/jquery.jscrollpane.js','../DO_NOT_UPLOAD/jquery.mCustomScrollbar.min.js','../DO_NOT_UPLOAD/jquery.simple-color.js','../DO_NOT_UPLOAD/quill.js','../DO_NOT_UPLOAD/extensions/ext-multiColor.js','../DO_NOT_UPLOAD/extensions/ext-objectPanel.js','../DO_NOT_UPLOAD/extensions/ext-objectLock.js','../DO_NOT_UPLOAD/extensions/ext-LayerPanel.js','../DO_NOT_UPLOAD/extensions/ext-productPricing.js','../DO_NOT_UPLOAD/extensions/ext-ProductMaskOverlayImage.js','../DO_NOT_UPLOAD/extensions/ext-textShape.js','../DO_NOT_UPLOAD/extensions/ext-recentColors.js','../DO_NOT_UPLOAD/extensions/ext-pickDesignColor.js','../DO_NOT_UPLOAD/extensions/ext-fliptools.js','../DO_NOT_UPLOAD/extensions/ext-distribute.js','../DO_NOT_UPLOAD/extensions/ext-imageEffect.js','../DO_NOT_UPLOAD/extensions/ext-dropbox.js','../DO_NOT_UPLOAD/extensions/ext-vdp.js','../DO_NOT_UPLOAD/extensions/ext-multiConfig.js','../DO_NOT_UPLOAD/extensions/ext-smartfields.js','../DO_NOT_UPLOAD/extensions/ext-undoRedo.js','../DO_NOT_UPLOAD/extensions/ext-corporate.js','../DO_NOT_UPLOAD/dependentoptions.js','../DO_NOT_UPLOAD/extensions/ext-ShowObjectSize.js','../DO_NOT_UPLOAD/extensions/ext-guidelines.js','../DO_NOT_UPLOAD/extensions/ext-getquote.js','../DO_NOT_UPLOAD/extensions/ext-threedpreview.js','../DO_NOT_UPLOAD/extensions/ext-mobileapp.js','../DO_NOT_UPLOAD/extensions/ext-nameNumber.js','../DO_NOT_UPLOAD/TextModule.js','../DO_NOT_UPLOAD/extensions/ext-marketPlace.js','../DO_NOT_UPLOAD/extensions/ext-background.js']
					  }
					}
				  }
				});
				grunt.loadNpmTasks('grunt-contrib-uglify');
			};";
				fwrite($myfile, $txt);
				
				$output = shell_exec('grunt uglify 2>&1');
				echo "<pre>";
				print_r($output);
			}
			if ($type == "product" || $type == "canvas" || $type == "both") {
								
				//minify required files
				
				$myfile = fopen("Gruntfile.js", "w") or die("Unable to open Gruntfile.js!");
				$txt = "module.exports = function(grunt) {
			  grunt.initConfig({
				  uglify: {
					options: {
					   mangle: {
						except: ['jQuery']
					  }
					},
					my_target: {
					  files: {
						'lib.studio.min.js': ['../js-hotkeys/jquery.hotkeys.min.js','../jquerybbq/jquery.bbq.min.js','../svgicons/jquery.svgicons.js', '../jquery-ui/jquery-ui.min.js', '../jgraduate/jpicker.js', '../jgraduate/jquery.jgraduate.min.js', '../spinbtn/JQuerySpinBtn.js', '../contextmenu/jquery.contextMenu.js', '../threejs/build/three.min.js', '../threejs/build/OBJLoader.js', '../threejs/build/OrbitControls.js', '../threejs/build/Detector.js','../threejs/build/Projector.js', '../threejs/build/CanvasRenderer.js', '../threejs/build/stats.min.js', '../picasa/picasa.js', '../flickr/jquery.flickr.js','../fancybox/jquery.fancybox.js', '../fancybox/helpers/jquery.fancybox-buttons.js','../jquery-ui/jquery.ui.touch-punch.js']
					  }
					}
				  }
				});
				grunt.loadNpmTasks('grunt-contrib-uglify');
			};";
				fwrite($myfile, $txt);
				
				$output = shell_exec('grunt uglify 2>&1');
				echo "<pre>";
				print_r($output);
			}
			

			
			if ($type == "product" || $type == "canvas" || $type == "both") {
								
				//minify required files
				
				$myfile = fopen("Gruntfile.js", "w") or die("Unable to open Gruntfile.js!");
		

			$txt = "module.exports = function(grunt) {
				grunt.initConfig({
					cssmin: {
						options: {
						mergeIntoShorthands: false,
						roundingPrecision: -1
						},
						target: {
							files: {
								'studio.min.css': ['../picasa/gallery.css','../css/jquery.jscrollpane.css','../css/jquery.mCustomScrollbar.css','../css/pick-a-color-1.1.8.min.css','../fancybox/jquery.fancybox.css','../fancybox/helpers/jquery.fancybox-buttons.css','../plupload/jquery.plupload.queue.css','../css/font-awesome.css','../css/jPicker.css','../css/jgraduate.css','../css/plugins.css']
							}
						}
					}
				});
				
				grunt.loadNpmTasks('grunt-contrib-cssmin');
				grunt.registerTask('default', ['cssmin']);
			};";
				fwrite($myfile, $txt);
				
				$output = shell_exec('grunt cssmin 2>&1');
				$error = shell_exec('grunt >> grunt.log 2>&1');
				echo "<pre>";
				print_r($output);
			}
			
		}else {
			echo "Invlaid type argument.";
		}
	}else {
		echo "Please specify type of studio.";
	}
?>