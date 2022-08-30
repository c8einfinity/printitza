<?php
/**
 * Copyright © 2019 Design 'N' Buy. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 * ALWAYS DO BETTER @a
 */
 
namespace Designnbuy\JobManagement\Controller\Adminhtml\Jobmanagement;

use Designnbuy\JobManagement\Model\Jobmanagement;

/**
 * Writer book save controller
 */
class Save extends \Designnbuy\JobManagement\Controller\Adminhtml\Jobmanagement
{
    protected function _beforeSave($model, $request)
    {
        $data = $request->getPost('data');
    }
}
