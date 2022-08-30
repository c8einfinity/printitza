/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry',
], function (Abstract, registry) {
    'use strict';

    return Abstract.extend({
        initialize: function () {
            this._super();
            var  provider = registry.get(this.provider),
                data = provider.data;
            console.log(data);
            if(data.id && !data.required){
                this.validation['required-entry'] = false;
                this.required(false);
                this.error(false);
            }

            return this;
        }
    });
});
