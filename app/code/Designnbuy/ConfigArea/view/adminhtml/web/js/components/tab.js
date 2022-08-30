define([
    'uiComponent',
    'jquery',
    'accordion',
    'Magento_Ui/js/modal/modal',
    "mage/backend/tabs",
], function (Component, $, modal) {
    'use strict';

    var cacheKey = 'modal-overlay';



    return Component.extend({

        initialize: function () {

            this._super();



        },
        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(['content']);

            return this;
        },

        render: function () {
            console.log('grid_tab');
            console.log($('#grid_tab'));

            $("#grid_tab").tabs();
        }

    });
});


