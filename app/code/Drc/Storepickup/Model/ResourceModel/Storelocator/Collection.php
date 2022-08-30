<?php
/**
 * {{Drc}}_{{Storepickup}} extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  {{Drc}}
 *                     @package   {{Drc}}_{{Storepickup}}
 *                     @copyright Copyright (c) {{2016}}
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Drc\Storepickup\Model\ResourceModel\Storelocator;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'storelocator_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'drc_storepickup_storelocator_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'storelocator_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Drc\Storepickup\Model\Storelocator', 'Drc\Storepickup\Model\ResourceModel\Storelocator');
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }
    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'storelocator_id', $labelField = 'store_title', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
