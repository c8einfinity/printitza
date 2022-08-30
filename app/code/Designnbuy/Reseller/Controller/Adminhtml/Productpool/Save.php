<?php

namespace Designnbuy\Reseller\Controller\Adminhtml\Productpool;

use Designnbuy\Reseller\Model\Productpool;

/**
 * Reseller Productpool save controller
 */
class Save extends \Designnbuy\Reseller\Controller\Adminhtml\Productpool
{
    /**
     * Before model save
     * @param  \Designnbuy\Productpool\Model\Productpool $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        $data = $request->getPost('data');
        
        /* Prepare relative links */
        $links = isset($data['links']) ? $data['links'] : ['productpool' => [], 'product' => []];
        if (is_array($links)) {
            foreach (['productpool', 'product'] as $linkType) {
                if (isset($links[$linkType]) && is_array($links[$linkType])) {
                    $linksData = [];
                    foreach ($links[$linkType] as $item) {
                        $linksData[$item['id']] = [
                            'position' => isset($item['position']) ? $item['position'] : 0
                        ];
                    }
                    $links[$linkType] = $linksData;
                } else {
                    $links[$linkType] = [];
                }
            }
            $model->setData('links', $links);
        }
    }
    protected function _afterSave($model, $request)
    {
        $resellerCollection = $this->_objectManager->create('Designnbuy\Reseller\Model\Resellers')->getCollection();
        foreach($resellerCollection as $reseller){
            $productPool = explode(",", $reseller->getProductpool());
            if (in_array($model->getEntityId(), $productPool)) {
                if($reseller->getProductCommission() != "") {
                    $this->_objectManager->create('Designnbuy\Reseller\Controller\Adminhtml\Resellers\Save')->addProductsToWebsites($reseller->getWebsiteId(),array($model->getEntityId()),$reseller->getCommissionType(),$reseller->getProductCommission(),$reseller->getStoreId());
                }
            }
        }
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
