<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Controller\Adminhtml\ColorCounter;

use Designnbuy\PrintingMethod\Model\ColorCounter;
/**
 * PrintingMethod printingmethod save controller
 */
class Save extends \Designnbuy\PrintingMethod\Controller\Adminhtml\ColorCounter
{
    /**
     * Before model save
     * @param  \Designnbuy\PrintingMethod\Model\PrintingMethod $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        /* Prepare dates */
        $dateFilter = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\Filter\Date');
        $data = $model->getData();

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
        $model->setData($data);



        /* Prepare relative links */
        $data = $request->getPost('data');
        $links = isset($data['links']) ? $data['links'] : null;
        if ($links && is_array($links)) {
            foreach (['printingmethod', 'product'] as $linkType) {
                if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                    $linksData = [];
                    foreach ($links[$linkType] as $item) {
                        $linksData[$item['id']] = [
                            'position' => $item['position']
                        ];
                    }
                    $links[$linkType] = $linksData;
                }
            }
            $model->setData('links', $links);
        }

        /* Prepare images */
        $data = $model->getData();
        foreach (['image'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                if (!empty($data[$key]['delete'])) {
                    $model->setData($key, null);
                } else {
                    if (isset($data[$key][0]['name']) && isset($data[$key][0]['tmp_name'])) {
                        $image = $data[$key][0]['name'];
                        $model->setData($key, PrintingMethod::BASE_MEDIA_PATH . DIRECTORY_SEPARATOR . $image);
                        $imageUploader = $this->_objectManager->get(
                            'Designnbuy\PrintingMethod\ImageUpload'
                        );
                        $imageUploader->moveFileFromTmp($image);
                    } else {
                        if (isset($data[$key][0]['name'])) {
                            $model->setData($key, $data[$key][0]['name']);
                        }
                    }
                }
            } else {
                $model->setData($key, null);
            }
        }


    }

}
