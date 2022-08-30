<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Controller\Adminhtml\Font\Upload;

use Designnbuy\Font\Controller\Adminhtml\Upload\Image\Action;

/**
 * Font featured image upload controller
 */
class TtfItalic extends Action
{
    /**
     * File key
     *
     * @var string
     */
    protected $_fileKey = 'ttfitalic';

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Designnbuy_Font::font');
    }

}
