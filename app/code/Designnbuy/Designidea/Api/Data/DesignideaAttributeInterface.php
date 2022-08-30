<?php

namespace Designnbuy\Designidea\Api\Data;

/**
 * @api
 */
interface DesignideaAttributeInterface extends \Designnbuy\Designidea\Api\Data\EavAttributeInterface
{
    /**#@+
     * Constants defined for keys of  data array
     */
    const ENTITY_TYPE_CODE = 'designnbuy_designidea';

    const NAME = 'name';
    
    const DESCRIPTION = 'description';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    
}
