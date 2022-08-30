<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Model;

/**
 * Clipart management model
 */
class ClipartManagement extends AbstractManagement
{
    /**
     * @var \Designnbuy\Clipart\Model\ClipartFactory
     */
    protected $_itemFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Designnbuy\Clipart\Model\ClipartFactory $clipartFactory
     */
    public function __construct(
        \Designnbuy\Clipart\Model\ClipartFactory $clipartFactory
    ) {
        $this->_itemFactory = $clipartFactory;
    }

    /**
     * Retrieve list of clipart by page type, term, store, etc
     *
     * @param  string $type
     * @param  string $term
     * @param  int $storeId
     * @param  int $page
     * @param  int $limit
     * @return bool
     */
    public function getList($type, $term, $storeId, $page, $limit)
    {
        try {
            $collection = $this->_itemFactory->create()->getCollection();
            $collection
                ->addActiveFilter()
                ->addStoreFilter($storeId)
                ->setOrder('position', 'ASC')
                ->setCurPage($page)
                ->setPageSize($limit);

            $type = strtolower($type);

            switch ($type) {
                case 'archive' :
                    $term = explode('-', $term);
                    if (count($term) < 2) {
                        return false;
                    }
                    list($year, $month) = $term;
                    $year = (int) $year;
                    $month = (int) $month;

                    if ($year < 1970) {
                        return false;
                    }
                    if ($month < 1 || $month > 12) {
                        return false;
                    }

                    $collection->addArchiveFilter($year, $month);
                    break;
                case 'author' :
                    $collection->addAuthorFilter($term);
                    break;
                case 'category' :
                    $collection->addCategoryFilter($term);
                    break;
                case 'search' :
                    $collection->addSearchFilter($term);
                    break;
                case 'tag' :
                    $collection->addTagFilter($term);
                    break;
            }

            $cliparts = [];
            foreach ($collection as $item) {
                $item->initDinamicData();
                $cliparts[] = $item->getData();
            }

            $result = [
                'cliparts' => $cliparts,
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
