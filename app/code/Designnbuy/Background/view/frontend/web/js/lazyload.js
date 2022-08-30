/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

/**
 * Backgrounds autload
 */
 define([
    'domReady!',
    'jquery'
], function (domReady, $) {
    'use strict';

    var Lazyload = function(options) {

        var that = this;

        /**
         * Lazyload default options.
         * @type {Object}
         */
        that.defaults = {
            expires: null,
            path: '/',
            domain: null,
            secure: false,
            lifetime: null
        };

        /**
         * Init options
         * @type {Object}
         */
        that.opt = $.extend(that.default, options);

        /**
         * Load new content
         */
        function startLoading()
        {
            if (that.opt.current_page < that.opt.last_page && !that.loading) {

                that.loading = true;
                $('.dnbbackground-show-onload').show();
                $('.dnbbackground-hide-onload').hide();

                $.ajax({
                    "url":that.opt.page_url[that.opt.current_page+1],
                    "cache":true
                }).success(function(data) {
                    var $html = $(data);
                    var ws = that.opt.list_wrapper;
                    var $nw = $html.find(ws);
                    if ($nw.length) {
                        $(ws).append($nw.html());
                        that.opt.current_page++;
                    }

                    endLoading();

                }).fail(function(xhr, ajaxOptions, thrownError) {
                    console.log(thrownError);
                    endLoading();
                });
            }
        }

        /**
         * On loading end
         */
        function endLoading() {
            that.loading = false;
            $('.dnbbackground-show-onload').hide();
            if (that.opt.current_page < that.opt.last_page) {
                $('.dnbbackground-hide-onload').show();
            }
        }

        /* Is not loading now */
        endLoading();

        /* If auto trigger enabled */
        if (that.opt.auto_trigger) {
            var $w = $(window);
            $w.scroll(function() {
                if ($w.scrollTop() + $w.height() >= $(document).height() - that.opt.padding)  {
                    startLoading();
                }
            });
        }

        /* On trigger element click */
        if (that.opt.trigger_element) {
            $(that.opt.trigger_element).click(function(){
                startLoading();
            });
        }
    }

    return function (options) {
        new Lazyload(options)
    };

});
