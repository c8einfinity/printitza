<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\ConfigArea\Controller\Adminhtml\ConfigArea;
use Magento\Framework\Controller\ResultFactory;
/**
 * ConfigArea configarea list controller
 */
class ConfigAreaSave extends \Designnbuy\ConfigArea\Controller\Adminhtml\ConfigArea
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $configAreaId = $this->getRequest()->getParam('id');
        $formPostValues = $this->getRequest()->getPostValue();

        if (isset($formPostValues)) {
            try {
                $model = $this->_configAreaFactory->create();
                $model->load($configAreaId);
                $model->addData($formPostValues);
                $model->save();

                $result = ['message' => 'Design area has been saved.'];
            } catch (\Exception $e) {
                $result = ['message' => 'Something went wrong while saving design area.', 'errorcode' => $e->getCode()];
            }
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
        }
    }
}
