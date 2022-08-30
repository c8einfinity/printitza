<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\ConfigArea\Controller\Adminhtml\ConfigArea\Upload;

use Designnbuy\ConfigArea\Controller\Adminhtml\Upload\Image\Action;

/**
 * ConfigArea gallery image upload controller
 */
class Gallery extends Action
{
    /**
     * File key
     *
     * @var string
     */
    protected $_fileKey = 'image';

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Designnbuy_ConfigArea::configarea');
    }

}
