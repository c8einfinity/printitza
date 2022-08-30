<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Model;

/**
 * Background management model
 */
class BackgroundManagement extends AbstractManagement
{
    /**
     * @var \Designnbuy\Background\Model\BackgroundFactory
     */
    protected $_itemFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Designnbuy\Background\Model\BackgroundFactory $backgroundFactory
     */
    public function __construct(
        \Designnbuy\Background\Model\BackgroundFactory $backgroundFactory
    ) {
        $this->_itemFactory = $backgroundFactory;
    }

    /**
     * Retrieve list of background by page type, term, store, etc
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
                ->setOrder('creation_time', 'DESC')
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

            $backgrounds = [];
            foreach ($collection as $item) {
                $item->initDinamicData();
                $backgrounds[] = $item->getData();
            }

            $result = [
                'backgrounds' => $backgrounds,
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
