<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Controller\Adminhtml\QuantityRange;

use Designnbuy\PrintingMethod\Model\QuantityRange;
/**
 * PrintingMethod QuantityRange save controller
 */
class Save extends \Designnbuy\PrintingMethod\Controller\Adminhtml\QuantityRange
{
    /**
     * Before model save
     * @param  \Designnbuy\PrintingMethod\Model\QuantityRange $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        $data = $model->getData();
        $model->setData($data);
    }

}
