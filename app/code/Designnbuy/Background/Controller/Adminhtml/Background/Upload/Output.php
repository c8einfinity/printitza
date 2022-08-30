<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Controller\Adminhtml\Background\Upload;

use Designnbuy\Background\Controller\Adminhtml\Upload\Image\Backgroundaction;

/**
 * Background featured image upload controller
 */
class Output extends Backgroundaction
{
    /**
     * File key
     *
     * @var string
     */
    protected $_fileKey = 'output';

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Designnbuy_Background::background');
    }

}
