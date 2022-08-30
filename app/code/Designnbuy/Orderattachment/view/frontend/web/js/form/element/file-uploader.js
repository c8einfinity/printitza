/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'Magento_Ui/js/form/element/file-uploader'
], function (Element) 
{   
    var uploadedFileLength;
    var imageIncrement;
    'use strict';
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
            var ProcessPr;
            if(uploadedFileLength){
                jQuery(".upload_image_block .meter label").text("Uploading... "+imageIncrement+" of "+uploadedFileLength+" files");
                imageIncrement++;
                ProcessPr = 100 / uploadedFileLength;
                var spanWidth = parseInt(jQuery(".upload_image_block .meter span").attr('orig-width'))
                
                var ProcessPrTotal = spanWidth + ProcessPr;
                console.log(ProcessPrTotal);
                jQuery(".upload_image_block .meter span").attr('orig-width',ProcessPrTotal);
                jQuery(".upload_image_block .meter span").css("width", ProcessPrTotal+'%');
            }

            file = this.processFile(file);

            this.isMultipleFiles ?
                this.value.push(file) :
                this.value([file]);

            return this;
        },
        onLoadingStart: function () {
            jQuery(".upload_image_block .meter").show();
            jQuery(".upload_image_block .meter span").attr('orig-width','0');
            jQuery(".upload_image_block .meter span").css("width", '0%');
        },
        onLoadingStop: function () {
            var aggregatedErrorMessages = [];
            jQuery(".upload_image_block .meter").hide();
            jQuery(".upload_image_block .meter span").attr('orig-width','0');
            jQuery(".upload_image_block .meter span").css("width", '0%');

            if (!this.aggregatedErrors.length) {
                return;
            }

            if (!this.isMultipleFiles) { // only single file upload occurred; use first file's error message
                aggregatedErrorMessages.push(this.aggregatedErrors[0].message);
            } else { // construct message from all aggregatedErrors
                _.each(this.aggregatedErrors, function (error) {
                    notification().add({
                        error: true,
                        message: '%s' + error.message, // %s to be used as placeholder for html injection

                        /**
                         * Adds constructed error notification to aggregatedErrorMessages
                         *
                         * @param {String} constructedMessage
                         */
                        insertMethod: function (constructedMessage) {
                            var errorMsgBodyHtml = '<strong>%s</strong> %s.<br>'
                                .replace('%s', error.filename)
                                .replace('%s', $t('was not uploaded'));

                            // html is escaped in message body for notification widget; prepend unescaped html here
                            constructedMessage = constructedMessage.replace('%s', errorMsgBodyHtml);

                            aggregatedErrorMessages.push(constructedMessage);
                        }
                    });
                });
            }

            this.notifyError(aggregatedErrorMessages.join(''));

            // clear out aggregatedErrors array for this completed upload chain
            this.aggregatedErrors = [];
        },
        onFilesChoosed: function (e, data) {
            uploadedFileLength = data.files.length;
            imageIncrement = 1;
            // no option exists in fileuploader for restricting upload chains to single files; this enforces that policy
            if (!this.isMultipleFiles) {
                data.files.splice(1);
            }
        },
    });
});