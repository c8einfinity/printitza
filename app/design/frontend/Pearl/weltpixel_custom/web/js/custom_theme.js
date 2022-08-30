require(['jquery', 'jquery/ui'], function($){
  jQuery(window).load(function($){
    if( /Android|webOS|iPhone|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
      //jQuery('.header.content > .header.links').prepend('[id^="store.links"]');
      	jQuery('.header.content > .header.links').appendTo('[id="store.links"]');
    }else{
    	var headerH = jQuery('.header-placeholder').height();
    	var totalH = jQuery(window).height() - headerH - 5;
      jQuery("nav.navigation .level0.submenu > li").css("max-height", totalH);
      jQuery("nav.navigation .level0.submenu > li .fullwidth-wrapper").css("max-height", totalH);
      
    }

  });

});