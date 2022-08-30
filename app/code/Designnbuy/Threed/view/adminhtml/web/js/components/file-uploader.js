/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/file-uploader',
    'Designnbuy_Threed/js/components/threed-config-area'
], function (Element, ThreedConfigArea) {
    'use strict';
    // threedConfigArea = ThreedConfigArea.call();
    return Element.extend({
        defaults: {
            fileInputName: ''
        },

        /**
         * Adds provided file to the files list.
         *
         * @param {Object} file
         * @returns {FileUploder} Chainable.
         */
        addFile: function (file) {
            var processedFile = this.processFile(file),
                tmpFile = [],
                resultFile = {
                'file': processedFile.file,
                'name': processedFile.name,
                'url': processedFile.url,
                'size': processedFile.size,
                'status': processedFile.status ? processedFile.status : 'new'
            };

            tmpFile[0] = resultFile;

            this.isMultipleFiles ?
                this.value.push(tmpFile) :
                this.value(tmpFile);

            return this;
        },

        /**
         * Handler of the file upload complete event.
         *
         * @param {Event} e
         * @param {Object} data
         */
        onFileUploaded: function (e, data) {

            var file    = data.result,
                error   = file.error;

            error ?
                this.notifyError(error) :
                this.addFile(file);

            if(!error){
                if(e && e.target.getAttribute('name') == 'map_image'){
                    // console.log('threedConfigArea',threedConfigArea.onCanvasRender('threeddesign_canvas'));
                }

            }

        }
    });
});
