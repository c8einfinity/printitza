
define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function ($, uiRegistry, component, modal) {
    'use strict';

    return component.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input'
        },
        jobHide: function(id) {            
            var downloadOutput = uiRegistry.get('index=download_output');
            //$('div[data-index="download_output"]').hide();
            if(downloadOutput.initialValue == 2) {
                
            }
        }
    });
});