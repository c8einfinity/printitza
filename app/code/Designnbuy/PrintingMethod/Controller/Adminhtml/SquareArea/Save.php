<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Controller\Adminhtml\SquareArea;

use Designnbuy\PrintingMethod\Model\SquareArea;
/**
 * PrintingMethod SquareArea save controller
 */
class Save extends \Designnbuy\PrintingMethod\Controller\Adminhtml\SquareArea
{
    /**
     * Before model save
     * @param  \Designnbuy\PrintingMethod\Model\SquareArea $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        /* Prepare dates */
        $dateFilter = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\Filter\Date');
        $data = $model->getData();

        $model->setData($data);

    }

}
