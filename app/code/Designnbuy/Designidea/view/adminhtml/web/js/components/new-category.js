/**
 * Copyright © 2016 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

define([
    'Designnbuy_Designidea/js/components/new-category'
], function (Select) {
    'use strict';

    return Select.extend({

        /**
         * Normalize option object.
         *
         * @param {Object} data - Option object.
         * @returns {Object}
         */
        parseData: function (data) {
            return {
                'is_active': data.category['is_active'],
                level: data.category['level'],
                value: data.category['category_id'],
                label: data.category['title'],
                parent: data.category['parent_id']
            };
        }
    });
});
