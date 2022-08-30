<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Model\Import;

/**
 * Abstract import model
 */
abstract class AbstractImport extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Connect to bd
     */
    protected $_connect;

    /**
     * @var array
     */
    protected $_requiredFields = [];

    /**
     * @var \Designnbuy\Background\Model\BackgroundFactory
     */
    protected $_backgroundFactory;

    /**
     * @var \Designnbuy\Background\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Designnbuy\Background\Model\TagFactory
     */
    protected $_tagFactory;

    /**
     * @var integer
     */
    protected $_importedBackgroundsCount = 0;

    /**
     * @var integer
     */
    protected $_importedCategoriesCount = 0;

    /**
     * @var integer
     */
    protected $_importedTagsCount = 0;

    /**
     * @var array
     */
    protected $_skippedBackgrounds = [];

    /**
     * @var array
     */
    protected $_skippedCategories = [];

    /**
     * @var array
     */
    protected $_skippedTags = [];


    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Designnbuy\Background\Model\BackgroundFactory $backgroundFactory,
     * @param \Designnbuy\Background\Model\CategoryFactory $categoryFactory,
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager,
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Background\Model\BackgroundFactory $backgroundFactory,
        \Designnbuy\Background\Model\CategoryFactory $categoryFactory,
        \Designnbuy\Background\Model\TagFactory $tagFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_backgroundFactory = $backgroundFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_tagFactory = $tagFactory;
        $this->_storeManager = $storeManager;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve import statistic
     * @return \Magento\Framework\DataObject
     */
    public function getImportStatistic()
    {
        return new \Magento\Framework\DataObject([
            'imported_backgrounds_count'      => $this->_importedBackgroundsCount,
            'imported_categories_count' => $this->_importedCategoriesCount,
            'skipped_backgrounds'             => $this->_skippedBackgrounds,
            'skipped_categories'        => $this->_skippedCategories,
            'imported_count'            => $this->_importedBackgroundsCount + $this->_importedCategoriesCount + $this->_importedTagsCount,
            'skipped_count'             => count($this->_skippedBackgrounds) + count($this->_skippedCategories) + count($this->_skippedTags),
            'imported_tags_count'       => $this->_importedTagsCount,
            'skipped_tags'              => $this->_skippedTags,
        ]);
    }

    /**
     * Prepare import data
     * @param  array $data
     * @return $this
     */
    public function prepareData($data)
    {
        if (!is_array($data)) {
            $data = (array) $data;
        }

        foreach($this->_requiredFields as $field) {
            if (empty($data[$field])) {
                throw new \Exception(__('Parameter %1 is required', $field), 1);
            }
        }

        foreach($data as $field => $value) {
            if (!in_array($field, $this->_requiredFields)) {
                unset($data[$field]);
            }
        }

        $this->setData($data);

        return $this;
    }

    /**
     * Execute mysql query
     */
    protected function _mysqliQuery($sql)
    {
        $result = mysqli_query($this->_connect, $sql);
        if (!$result) {
            throw new \Exception(
                __('Mysql error: %1.', mysqli_error($this->_connect))
            );
        }

        return $result;
    }
}
