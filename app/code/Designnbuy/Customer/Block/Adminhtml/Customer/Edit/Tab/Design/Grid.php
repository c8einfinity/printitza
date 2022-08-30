<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Block\Adminhtml\Customer\Edit\Tab\Design;

/**
 * Adminhtml newsletter queue grid block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Queue\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Designnbuy\Customer\Model\ResourceModel\Design\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\Customer\Model\ResourceModel\Design\Grid\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('start_at');
        $this->setDefaultDir('desc');
        $this->setSortable(false);
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
        $this->setUseAjax(true);

        $this->setEmptyText(__('No Designs Found'));
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('customer/*/design', ['_current' => true]);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        /** @var $collection \Designnbuy\Customer\Model\ResourceModel\Design\Grid\Collection */
        $collection = $this->_collectionFactory->create()
            ->addFieldToFilter(
            'customer_id',
                $this->_coreRegistry->registry('current_customer_id')
        );
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'design_id',
            ['header' => __('ID'), 'align' => 'left', 'index' => 'design_id' , 'filter'   => false, 'sortable' => false, 'width' => 10]
        );

        $this->addColumn('design_name', ['header' => __('Design Name'), 'filter'   => false, 'sortable' => false, 'index' => 'design_name']);
        
        $this->addColumn(
            'image',
            [
                'header' => __('Image'),
                'sortable' => false,
                'index' => 'image',
                'filter'   => false,
                'renderer'  => 'Designnbuy\Customer\Block\Adminhtml\Customer\Edit\Tab\Design\Grid\Renderer\Image'

            ]
        );
        /*$this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'align' => 'center',
                'filter' => 'Magento\Customer\Block\Adminhtml\Edit\Tab\Design\Grid\Filter\Status',
                'index' => 'queue_status',
                'renderer' => 'Magento\Customer\Block\Adminhtml\Edit\Tab\Design\Grid\Renderer\Status'
            ]
        );*/

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'align' => 'center',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Designnbuy\Customer\Block\Adminhtml\Customer\Edit\Tab\Design\Grid\Renderer\Action'
            ]
        );

        return parent::_prepareColumns();
    }
}
