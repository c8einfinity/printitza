<?php

namespace Designnbuy\Template\Api\Data\Category;

/**
 * @api
 */
interface CategoryAttributeInterface extends \Designnbuy\Template\Api\Data\EavAttributeInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const ENTITY_TYPE_CODE = 'designnbuy_template_category';

    const NAME = 'name';
    
    const DESCRIPTION = 'description';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    
}
