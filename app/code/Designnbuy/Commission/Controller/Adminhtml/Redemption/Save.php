<?php
namespace Designnbuy\Commission\Controller\Adminhtml\Redemption;

use Designnbuy\Commission\Model\Redemption;

/**
 * Designer Redemption save controller
 */
class Save extends \Designnbuy\Commission\Controller\Adminhtml\Redemption
{
    /**
     * Before model save
     * @param  \Designnbuy\Commission\Model\Redemption $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        $data = $request->getPost('data');
    }

    /**
     * Filter request params
     * @param  array $data
     * @return array
     */
    protected function filterParams($data)
    {
        /* Prepare dates */
        $dateFilter = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\Filter\Date');

        $filterRules = [];
        foreach (['publish_time', 'custom_theme_from', 'custom_theme_to'] as $dateField) {
            if (!empty($data[$dateField])) {
                $filterRules[$dateField] = $dateFilter;
            }
        }

        $inputFilter = new \Zend_Filter_Input(
            $filterRules,
            [],
            $data
        );

        $data = $inputFilter->getUnescaped();

        return $data;
    }
}
