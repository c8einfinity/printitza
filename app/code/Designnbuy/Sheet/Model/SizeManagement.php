<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Model;

/**
 * Size management model
 */
class SizeManagement extends AbstractManagement
{
    /**
     * @var \Designnbuy\Sheet\Model\SizeFactory
     */
    protected $_itemFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Designnbuy\Sheet\Model\SizeFactory $sizeFactory
     */
    public function __construct(
        \Designnbuy\Sheet\Model\SizeFactory $sizeFactory
    ) {
        $this->_itemFactory = $sizeFactory;
    }

    /**
     * Retrieve list of size by page type, term, store, etc
     *
     * @param  string $type
     * @param  string $term
     * @param  int $storeId
     * @param  int $page
     * @param  int $limit
     * @return string
     */
    public function getList($type, $term, $storeId, $page, $limit)
    {
        try {
            $collection = $this->_itemFactory->create()->getCollection();
            $collection
                ->addActiveFilter()
                ->addStoreFilter($storeId)
                ->setOrder('title', 'DESC')
                ->setCurPage($page)
                ->setPageSize($limit);

            $type = strtolower($type);

            switch ($type) {

                case 'search':
                    $collection->addSearchFilter($term);
                    break;

            }

            $sizes = [];
            foreach ($collection as $item) {
                $item->initDinamicData();
                $sizes[] = $item->getData();
            }

            $result = [
                'sizes' => $sizes,
                'total_number' => $collection->getSize(),
                'current_page' => $collection->getCurPage(),
                'last_page' => $collection->getLastPageNumber(),
            ];

            return json_encode($result);
        } catch (\Exception $e) {
            return false;
        }
    }
}
