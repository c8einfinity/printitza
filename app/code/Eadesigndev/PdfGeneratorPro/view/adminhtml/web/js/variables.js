
define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'jquery/ui',
    'prototype',
], function (jQuery, $t) {

    window.XteaVariables = {
        textareaElementId: null,
        variablesContent: null,
        dialogWindow: null,
        dialogWindowId: 'variables-chooser',
        overlayShowEffectOptions: null,
        overlayHideEffectOptions: null,
        insertFunction: 'XteaVariables.insertVariable',
        init: function (textareaElementId, insertFunction) {
            if ($(textareaElementId)) {
                this.textareaElementId = textareaElementId;
            }
            if (insertFunction) {
                this.insertFunction = insertFunction;
            }
        },

        resetData: function () {
            this.variablesContent = null;
            this.dialogWindow = null;
        },

        openVariableChooser: function (variables) {
            if (this.variablesContent == null && variables) {
                this.variablesContent = '<ul class="insert-variable">';
                variables.each(function (variableGroup) {
                    if (variableGroup.label && variableGroup.value) {
                        this.variablesContent += '<li><b>' + variableGroup.label + '</b></li>';
                        (variableGroup.value).each(function (variable) {
                            if (variable.value && variable.label) {
                                this.variablesContent += '<li>' +
                                    this.prepareVariableRow(variable.value, variable.label) + '</li>';
                            }
                        }.bind(this));
                    }
                }.bind(this));
                this.variablesContent += '</ul>';
            }
            if (this.variablesContent) {
                this.openDialogWindow(this.variablesContent);
            }
            this.resetData();
        },
        openDialogWindow: function (variablesContent) {
            var windowId = this.dialogWindowId;
            jQuery('<div id="' + windowId + '">' + XteaVariables.variablesContent + '</div>').modal({
                title: $t('Insert Variable...'),
                type: 'slide',
                buttons: [],
                closed: function (e, modal) {
                    modal.modal.remove();
                }
            });

            jQuery('#' + windowId).modal('openModal');

            variablesContent.evalScripts.bind(variablesContent).defer();
        },
        closeDialogWindow: function () {
            jQuery('#' + this.dialogWindowId).modal('closeModal');
        },
        prepareVariableRow: function (varValue, varLabel) {
            var value = (varValue).replace(/"/g, '&quot;').replace(/'/g, '\\&#39;');
            var content = '<a href="#" onclick="' + this.insertFunction + '(\'' + value + '\');return false;">' + varLabel + '</a>';
            return content;
        },
        insertVariable: function (value) {
            var windowId = this.dialogWindowId;
            jQuery('#' + windowId).modal('closeModal');
            var textareaElm = $(this.textareaElementId);
            if (textareaElm) {
                var scrollPos = textareaElm.scrollTop;
                updateElementAtCursor(textareaElm, value);
                textareaElm.focus();
                textareaElm.scrollTop = scrollPos;
                jQuery(textareaElm).change();
                textareaElm = null;
            }
            return;
        }
    };

    window.XteaVariablePlugin = {
        editor: null,
        variables: null,
        textareaId: null,
        setEditor: function (editor) {
            this.editor = editor;
        },
        loadChooser: function (url, textareaId) {
            var fieldVal = jQuery('input[name=variables_entity_id]').val();
            if(fieldVal == 0 || !fieldVal){
                alert($t('Please select a valid source...'));
                return;
            }

            this.textareaId = textareaId;
            new Ajax.Request(url, {
                parameters: {'variables_entity_id':fieldVal},
                onComplete: function (transport) {
                    if (transport.responseText.isJSON()) {
                        XteaVariables.init(null, 'XteaVariablePlugin.insertVariable');
                        this.variables = transport.responseText.evalJSON();
                        this.openChooser(this.variables);
                    }
                }.bind(this)
            });
            return;
        },
        openChooser: function (variables) {
            XteaVariables.openVariableChooser(variables);
        },
        insertVariable: function (value) {

            var prefix = this.textareaId.replace('template_',''),
                editorIdPrefix = prefix,
                editor = tinymce.get(editorIdPrefix+ '_' + this.textareaId);

            if (this.textareaId && editor === undefined) {
                XteaVariables.init(editorIdPrefix + '_template_' + editorIdPrefix);
                XteaVariables.insertVariable(value);
            } else {
                XteaVariables.closeDialogWindow();
                editor.execCommand('mceInsertContent', false, value);
            }
        }
    };

});