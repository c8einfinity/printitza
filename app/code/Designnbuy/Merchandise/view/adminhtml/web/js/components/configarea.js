/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'Magento_Ui/js/form/components/button',
    'uiLayout',
    'mageUtils',
    "fabric",
    "Designnbuy_ConfigArea/js/config_area"
], function (registry, button, layout, utils, fabric, configArea) {
    'use strict';

    return button.extend({
        defaults: {
            additionalClasses: {},
            displayArea: 'outsideGroup',
            displayAsLink: false,
            elementTmpl: 'ui/form/element/button',
            template: 'ui/form/components/button/simple',
            visible: true,
            disabled: false,
            title: ''
        },

        /**
         * Initializes component.
         *
         * @returns {Object} Chainable.
         */
        initialize: function () {
            return this._super()
                ._setClasses();
        },

        /** @inheritdoc */
        initObservable: function () {
            return this._super()
                .observe([
                    'visible',
                    'disabled',
                    'title'
                ]);
        },



        /**
         * Performs configured actions
         */
        action: function () {
            console.log('this',this);
            this.actions.forEach(this.applyAction, this);
        },
        /**
         * Apply action on target component,
         * but previously create this component from template if it is not existed
         *
         * @param {Object} action - action configuration
         */
        applyAction: function (action) {
            var targetName = action.targetName,
                params = (action.params) ? [action.params.last()] : [],
                actionName = action.actionName,
                target;

            if (!registry.has(targetName)) {
                console.log('this');
                this.getFromTemplate(targetName);
            }
            target = registry.async(targetName);
            console.log('target',target);
            console.log('targetName',targetName);
            console.log('actionName',actionName);
            console.log('typeof target ',typeof target );
            if (target && typeof target === 'function' && actionName) {
                if (params.length > 1) {
                    params.shift();
                }
                params.unshift(actionName);
                console.log('params',params);

                target.apply(target, params);
            }
        },
        /**
         * Close modal
         */
        closeModal: function () {
            this._super();

        },
        actionDone: function () {
            this._super();
        },
        /**
         * Create target component from template
         *
         * @param {Object} targetName - name of component,
         * that supposed to be a template and need to be initialized
         */
        getFromTemplate: function (targetName) {
            var parentName = targetName.split('.'),
                index = parentName.pop(),
                child;

            parentName = parentName.join('.');
            child = utils.template({
                parent: parentName,
                name: index,
                nodeTemplate: targetName
            });
            layout([child]);
        },

        /**
         * Extends 'additionalClasses' object.
         *
         * @returns {Object} Chainable.
         */
        _setClasses: function () {
            if (typeof this.additionalClasses === 'string') {
                this.additionalClasses = this.additionalClasses
                    .trim()
                    .split(' ')
                    .reduce(function (classes, name) {
                            classes[name] = true;

                            return classes;
                        }, {}
                    );
            }

            return this;
        }
    });
});
