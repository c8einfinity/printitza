define([
    'uiComponent'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
        },

        /**
         * Invokes initialize method of parent class,
         * contains initialization logic
         */
        initialize: function () {
            this._super();


            return this;
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(['visible',
                    'disabled',
                    'title', 'content']);

            return this;
        },

    });
});