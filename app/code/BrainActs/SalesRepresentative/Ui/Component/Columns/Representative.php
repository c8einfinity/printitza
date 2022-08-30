<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Ui\Component\Columns;

/**
 * Class Representative
 * @author BrainActs Core Team <support@brainacts.com>
 */
class Representative extends \Magento\Ui\Component\Listing\Columns\Column
{

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $value = $item[$this->getData('name')];

                if (empty($value)) {
                    continue;
                }

                $list = explode(',', $value);

                $size = count($list);

                if ($size > 1) {
                    $item[$this->getData('name')] = $item['representative'];
                } elseif ($size == 1) {
                    $item[$this->getData('name')] = $item['representative'];
                }
            }
        }
        return $dataSource;
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        return;
    }
}
