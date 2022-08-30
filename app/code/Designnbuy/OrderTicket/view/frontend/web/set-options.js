/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        $(element).on('mage.setUpOrderTicketOptions', function (e, orderticketTrackInfo) {
            orderticketTrackInfo.options.deleteLabelUrl = config.deleteLabelUrl;
            orderticketTrackInfo.options.deleteMsg = config.deleteMsg;
        });
    };
});
