<?php
namespace Designnbuy\Reseller\Block\Adminhtml\Resellers\Edit\Tab;

class Productpool extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Designnbuy\Reseller\Model\ResourceModel\Productpool\CollectionFactory
     */
    protected $poolCollectionFactory;
    /**
     * Contact factory
     *
     * @var resellerFactory
     */
    protected $resellerFactory;
    /**
     * @var  \Magento\Framework\Registry
     */
    protected $registry;

    protected $_objectManager = null;

    protected $_reseller;
    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param resellerFactory $attachmentFactory
     * @param \Designnbuy\Reseller\Model\ResourceModel\Productpool\CollectionFactory $poolCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Designnbuy\Reseller\Model\ResellersFactory $resellerFactory,
        \Designnbuy\Reseller\Model\ResourceModel\Productpool\CollectionFactory $poolCollectionFactory,
        array $data = []
    ) {
        $this->resellerFactory = $resellerFactory;
        $this->poolCollectionFactory = $poolCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->registry = $registry;
        parent::__construct($context, $backendHelper, $data);
    }
    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(false);
        $this->setUseAjax(true);
        //$this->setDefaultFilter(array('in_products' => 1));
    }

    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
        $collection = $this->poolCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $this->setCollection($collection);
        return parent::_prepareCollection();
        return $this;
    }


    /**
     * add Column Filter To Collection
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->getSelectedProductpool();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        //$model = $this->_objectManager->get('\Designnbuy\Corporate\Model\Corporate');
        $this->addColumn(
            'in_products',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_products',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product Pool ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index' => 'title',
                'class' => 'col-title',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'creation_time',
            [
                'header' => __('Created At'),
                'index' => 'creation_time',
                'type' => "date",
                'class' => 'col-time',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'update_time',
            [
                'header' => __('Updated At'),
                'index' => 'update_time',
                'type' => "date",
                'class' => 'col-time',
                'width' => '50px',
            ]
        );

        $this->addColumn(
            'sort_order',
            [
                'header' => __('Sort Order'),
                'name' => 'sort_order',
                'index' => 'sort_order',
                'width' => '50px',
                'editable' => true,
            ]
        );

        return parent::_prepareColumns();
    }
    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/ProductpoolsGrid', ['_current' => true]);
    }
    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }


    protected function _getSelectedProducts()
    {
        $products = $this->getProducts();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedProducts());
        }

        return $products;
    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        $reseller = $this->getReseller();
        $selected = $reseller->getProductpool(); 

        $selectedExpolode = explode(",", $selected);

        $selected = array();
        foreach ($selectedExpolode as $key => $value) {
            $selected[$value] = array();
        }

        if (!is_array($selected)) {
            $selected = [];
        }
        return $selected;
    }

    protected function getReseller()
    {
        $resellerId = $this->getRequest()->getParam('reseller_id');
        $reseller   = $this->resellerFactory->create();
        if ($resellerId) {
            $reseller->load($resellerId);
        }
        return $reseller;
    }


    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }
}
