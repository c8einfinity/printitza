<?php

namespace Designnbuy\Notifications\Controller\Adminhtml\Notifications;

use Designnbuy\Notifications\Model\Notifications;

/**
 * Notifications save controller
 */
class Save extends \Designnbuy\Notifications\Controller\Adminhtml\Notifications
{
    /**
     * Before model save
     * @param  \Designnbuy\Notifications\Notifications\Notifications $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        $data = $request->getPost('data');
    }
}
