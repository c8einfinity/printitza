/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/single-checkbox',
    'uiRegistry'
], function (Checkbox, registry) {
    'use strict';

    return Checkbox.extend({
        defaults: {
            clearing: false,
            parentContainer: '',
            parentSelections: '',
            changer: ''
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super().
            observe('elementTmpl');

            return this;
        },

        /**
         * @inheritdoc
         */
        initConfig: function () {
            this._super();
            /*console.log('initConfig');
            var designidea_id = registry.get('product_form.product_form.advanced_design_modal.merchandise-personalisation.container_designidea_id.designidea_id');
            console.log('designidea_id.value()',designidea_id.value());
            var records = require('uiRegistry').get('index = designs');
            console.log('records',records.elems);
            records.elems.each(function (record) {
                console.log('record',record);
                record.elems.filter(function (comp) {
                    console.log('designidea_id');
                    if(designidea_id !== undefined && comp.value() == designidea_id.value()){
                        console.log('designidea_id value');
                        jQuery('#'+comp.uid).prop('checked', true).trigger('change')

                    }
                });
            });*/

            return this;
        },

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            console.log('newChecked',this);
            if (this.prefer === 'radio' && this.checked() && !this.clearing) {
                console.log('newChecked',this);
                this.clearValues();
            }

            //this._super();
        },

        onCheckedChanged: function (newChecked) {
            var records = require('uiRegistry').get('index = designs'),
                index = this.index,
                uid = this.uid;
            console.log('this.index',this.index);
            console.log('this.uid',this.uid);

            records.elems.each(function (record) {
                record.elems.filter(function (comp) {
                    if (typeof comp.checked !== 'undefined') {
                        if (index == comp.index) {
                            console.log('comp.index', comp.index);
                            console.log('comp.uid', comp.uid);
                            console.log('comp.uid', comp);

                            comp.checked(false);

                        } else {
                            comp.checked(true);
                        }
                    }

                }).each(function (comp) {
                    comp.clearing = true;
                    comp.clear();
                    comp.clearing = false;
                });
            });

            /*var designidea_id = registry.get('product_form.product_form.advanced_design_modal.merchandise-personalisation.container_designidea_id.designidea_id');
            if(designidea_id.uid){
                designidea_id.value(this.value());
            }*/
        },


        /**
         * Clears values in components like this.
         */
        clearValues: function () {
           /* var records = require('uiRegistry').get('index = designs'),
                index = this.index,
                uid = this.uid;

            records.elems.each(function (record) {
                record.elems.filter(function (comp) {
                    return comp.index === index && comp.uid !== uid;
                }).each(function (comp) {
                    comp.clearing = true;
                    comp.clear();
                    comp.clearing = false;
                });
            });*/
        }

    });
});
