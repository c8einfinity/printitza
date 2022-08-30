module.exports = function(grunt) {
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
			};