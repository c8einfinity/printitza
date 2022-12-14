var productPage = {
	init: function () {
		jQuery('.togglet').bind('click', function() {
			setTimeout(function() {jQuery(window).trigger('resize')}, 300);
		});
	},

	load: function () {
		this.action();
		this.mageSticky();
		this.addMinHeight();
	},

	ajaxComplete: function () {
		this.mageSticky();
		this.adjustHeight();
	},

	resize: function () {
		this.action();
		this.adjustHeight();
		this.mageSticky();
	},

	adjustHeight: function() {
		// adjust left media height as well, in case it is smallers
		var media = jQuery('.product.media'),
			mediaGallery = jQuery('.product.media .gallery'),
			infoMain = jQuery('.product-info-main');

		if ( jQuery('body').hasClass('wp-device-xs') ||
			jQuery('body').hasClass('wp-device-s') ||
			jQuery('body').hasClass('wp-device-m')
		) {
			media.height('auto');
		} else {
			if ( ( mediaGallery.height() > 0 ) && ( mediaGallery.height() < infoMain.height())) {
				media.height(infoMain.height());
			}
		}
	},

	mageSticky: function () {
		var positionProductInfo = window.positionProductInfo;

		if (positionProductInfo == 1) {
			if (jQuery('body').hasClass('product-page-v2')) {
				jQuery('.product-info-main.product_v2.cart-summary').mage('sticky', {
					container: '.product-top-main.product_v2',
					spacingTop: 100
				});
			}
			if (jQuery('body').hasClass('product-page-v4')) {
				jQuery('.product-info-main.product_v4.cart-summary').mage('sticky', {
					container: '.product-top-main.product_v4',
					spacingTop: 25
				});
			}

		} else {
			if (jQuery('body').hasClass('product-page-v2') || jQuery('body').hasClass('product-page-v4')) {
				jQuery('.product-info-main.product_v2.cart-summary, .product-info-main.product_v4.cart-summary').addClass("no-sticky-product-page");
			}
		}

	},

	action: function () {
		var media = jQuery('.product.media.product_v2'),
			media_v4 = jQuery('.product.media.product_v4'),
			swipeOff = jQuery('.swipe_desktop_off #swipeOff');

		if(jQuery(window).width() > 768) {
			media.addClass('v2');
			media_v4.addClass('v4');
		} else {
			media.removeClass('v2');
			media_v4.removeClass('v4');
		}

		if(jQuery(window).width() > 1024) {
			swipeOff.addClass('active');
		} else {
			swipeOff.removeClass('active');
		}
	},

	addMinHeight: function() {
		var media_v4 = jQuery('.product.media.product_v4');
		if (media_v4.length) {
			var mediaContainer = media_v4.find('.gallery-placeholder');
			this.waitForEl('.fotorama__loaded--img', function() {
				var prodImg = mediaContainer.find('.fotorama__loaded--img').first();
				mediaContainer.css('min-height', prodImg.outerHeight());
			});
		}
	},

	waitForEl: function(selector, callback) {
		var that = this;
		if (jQuery(selector).length) {
			callback();
		} else {
			setTimeout(function() {
				that.waitForEl(selector, callback);
			}, 500);
		}
	},

	bindStickyScroll: function() {
		var productInfoMain = jQuery('.product-info-main'),
			productInfoMainLeft = parseInt(productInfoMain.offset().left),
			productInfoMainWidth = parseInt(productInfoMain.width()),
			bottomCorrection = '27px',
			leftCorrection = productInfoMainLeft - 15+ 'px',
			topOffset = 136,
			topOffsetV2 = 74,
			lastScrollTop = -50,
			fixedPos = 0;

		jQuery(window).on('scroll mousedown wheel DOMMouseScroll mousewheel keyup', function(e) {
			var autoScroll = false;
			if(e.which == 1 && e.type == 'mousedown') {
				autoScroll = true;
			}
			var scrollTopPos = parseInt(jQuery(window).scrollTop()),
				scrollPos = parseInt(jQuery(window).scrollTop()) + parseInt(jQuery(window).outerHeight()),
				productInfoMainBottom = parseInt(productInfoMain.offset().top) + parseInt(productInfoMain.outerHeight()),
				topPos = scrollTopPos + parseInt(productInfoMain.outerHeight()) + 95,
				productInfoMainTop = parseInt(productInfoMain.offset().top) - parseInt(productInfoMain.css('top')),
				v2MediaBlock = jQuery('.product.media.product_v2.v2'),
				v4MediaBlock = jQuery('.product.media.product_v4.v4'),
				footerEl = v2MediaBlock.length > 0 ? v2MediaBlock : v4MediaBlock,
				footerOffset = footerEl.length ? parseInt(footerEl.offset().top) + parseInt(footerEl.outerHeight() - 20) : 0,
				galleryHeight = parseInt(jQuery('.gallery-placeholder').outerHeight());
				scrollDir = 'dwn';

			jQuery('.gallery-placeholder').css('height', galleryHeight+'px');
			if(scrollTopPos >  lastScrollTop){
				scrollDir = 'dwn';
			} else {
				scrollDir = 'up';
			}

			if(footerEl.hasClass('product_v4')) {
				footerEl.addClass('pp-floating-v4')
			}

			if(jQuery('body').hasClass('product-page-v2')) {
				if(scrollTopPos >= 0 && scrollTopPos <= topOffsetV2){
					productInfoMain.removeClass('pp-fixed').removeAttr('style');
				} else if(scrollTopPos >= topOffsetV2 && productInfoMainBottom <= footerOffset) {
					productInfoMain.addClass('pp-fixed').css({
						'left': productInfoMainLeft+'px',
						'width': productInfoMainWidth+'px'});
				} else if(productInfoMainTop > topOffsetV2 && scrollDir=='up' && productInfoMainBottom <= footerOffset && topPos <= footerOffset) {
					productInfoMain.addClass('pp-fixed').removeAttr('style').css({
						'left': productInfoMainLeft+'px',
						'width': productInfoMainWidth+'px'});
				} else if(productInfoMainBottom >= footerOffset && topPos >= footerOffset && scrollTopPos >= fixedPos) {
					if(fixedPos == 0) fixedPos = scrollTopPos;
					if(autoScroll || scrollDir == 'dwn'){
						productInfoMain.removeClass('pp-fixed').removeAttr('style').css({
							'margin':'0 !important',
							'padding':'0 !important',
							'bottom': bottomCorrection,
							'right' : '0',
							'position' : 'absolute',
							'width': productInfoMainWidth+'px'});

					} else if(!autoScroll && scrollDir == 'up') {
						productInfoMain.addClass('pp-fixed').removeAttr('style').css({
							'left': productInfoMainLeft+'px',
							'width': productInfoMainWidth+'px'});
					}

				} else if(scrollTopPos <= fixedPos && scrollDir == 'up') {
					fixedPos = 0;
					productInfoMain.addClass('pp-fixed').removeAttr('style').css({
						'left': productInfoMainLeft+'px',
						'width': productInfoMainWidth+'px'});
				} else {
					productInfoMain.removeAttr('style').css({'left': productInfoMainLeft+'px', 'width': productInfoMainWidth+'px'});
				}
			}

			if(jQuery('body').hasClass('product-page-v4')) {
				if(scrollTopPos >= 0 && scrollTopPos <= topOffset){
					productInfoMain.removeClass('pp-fixed').addClass('pp-floating-v4').removeAttr('style');
				} else if(scrollTopPos >= topOffset && productInfoMainBottom <= footerOffset ) {
					productInfoMain.addClass('pp-fixed').removeClass('pp-floating-v4').removeAttr('style').css({
						'left': productInfoMainLeft+'px',
						'width': productInfoMainWidth+'px'});
				} else if(productInfoMainTop > topOffset && scrollDir=='up' && productInfoMainBottom <= footerOffset && topPos <= footerOffset) {
					productInfoMain.addClass('pp-fixed').removeClass('pp-floating-v4').removeAttr('style').css({
						'left': productInfoMainLeft+'px',
						'width': productInfoMainWidth+'px'});
				} else if(productInfoMainBottom >= footerOffset && topPos >= footerOffset && scrollTopPos >= fixedPos) {
					if(fixedPos == 0) fixedPos = scrollTopPos;
					productInfoMain.addClass('pp-floating-v4').removeClass('pp-fixed').css({
						'margin':'0 !important',
						'padding':'0 !important',
						'bottom': bottomCorrection,
						'left': leftCorrection,
						'width': productInfoMainWidth+'px'});
				} else if(scrollTopPos <= fixedPos && scrollDir == 'up') {
					fixedPos = 0;
					productInfoMain.addClass('pp-fixed').removeClass('pp-floating-v4').removeAttr('style').css({'left': productInfoMainLeft+'px', 'width': productInfoMainWidth+'px'});
				} else {
					productInfoMain.removeAttr('style').css({
						'left': productInfoMainLeft+'px',
						'width': productInfoMainWidth+'px'});
				}
			}

			lastScrollTop = scrollTopPos - 50;
		})
	},

	isMobileCheck: function() {
		var mobileClasses = ['wp-device-xxs','wp-device-xs','wp-device-s','wp-device-m'];
		for (index = 0; index < mobileClasses.length; ++index) {
			if(jQuery('body').hasClass(mobileClasses[index])) {
				return true;
			}
		}
		return false;
	}
};

require(['jquery', 'productPage', 'mage/mage', 'mage/ie-class-fixer', 'mage/gallery/gallery'],
	function ($) {
		$(document).ready(function () {
			productPage.init();
		});

		$(window).load(function () {
			productPage.load();
			var positionProductInfo = window.positionProductInfo;
			var isMobileCheck = productPage.isMobileCheck();
			if (positionProductInfo == 1 && !isMobileCheck) {
				productPage.bindStickyScroll();
			}
			$('.product-info-main').removeClass('pp-floating-v4');
			if(!isMobileCheck && $('.product-info-main').hasClass('product_v4')) {
				$('.product-info-main').addClass('pp-floating-v4');
			}

		});

		$(document).ajaxComplete(function () {
			productPage.ajaxComplete();
		});


		var reinitTimer;
		$(window).on('resize', function () {
			clearTimeout(reinitTimer);
			reinitTimer = setTimeout(productPage.resize(), 300);
		});

		var headerSection = $('.page-wrapper div.page-header');
		var stickyElement = $('.product-info-main.cart-summary');
		/*$(window).scroll(function() {
            if (headerSection.hasClass('sticky-header')) {
                $(stickyElement.children().get(0)).css('padding-top', headerSection.height());
            } else {
                $(stickyElement.children().get(0)).css('padding-top', 0);
            }
        });*/
	}
);