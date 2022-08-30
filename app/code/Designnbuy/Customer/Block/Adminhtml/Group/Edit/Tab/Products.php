<?php
namespace Designnbuy\Customer\Block\Adminhtml\Group\Edit\Tab;

use \Designnbuy\Customer\Model\GroupFactory;

class Products extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var \Designnbuy\Productattach\Model\Productattach
     */
    private $attachModel;

    /**
     * @var GroupFactory
     */
    private $contactFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * Products constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param GroupFactory $contactFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Designnbuy\Customer\Model\Group $attachModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        GroupFactory $contactFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Designnbuy\Productattach\Model\Productattach $attachModel,
        array $data = []
    ) {
        $this->contactFactory = $contactFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->registry = $registry;
        $this->attachModel = $attachModel;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id') != "") {
            $this->setDefaultFilter(['in_product' => 1]);
        }
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    public function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_product') {
            
            $productIds = $this->_getSelectedProducts();
            
            if (empty($productIds)) {
                return $this;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * prepare collection
     */
    public function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToSelect('price');
        $collection->addAttributeToFilter('status',1);
        $collection->addFieldToFilter(
            'visibility',
            ['neq' => 1]
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    public function _prepareColumns()
    {

        $model = $this->attachModel;

        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'names',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '50px',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productsgrid', ['_current' => true]);
    }

    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    public function _getSelectedProducts()
    {
        if($this->getRequest()->getParam('id') == 0){

            $contact = $this->getContact();
            $selected = $contact->getProducts();
            
            
            $collection = $this->productCollectionFactory->create();
            $collection->addAttributeToSelect('entity_id');
            $collection->addAttributeToFilter('status',1);
            if ($selected != "" && !is_array($selected)) {
                $selected = explode('&',$selected);
            
                $collection->addAttributeToFilter('entity_id',
                    ['nin' => $selected]
                );
            }
            $collection->addFieldToFilter(
                'visibility',
                ['neq' => 1]
            );
            return $collection->getAllIds();
            
        } else {

            $contact = $this->getContact();
            $selected = $contact->getProducts();
            if ($selected != "" && !is_array($selected)) {
                $selected = explode('&',$selected);
            }
            return $selected;
        }


    }

    /**
     * Retrieve selected products
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        $contact = $this->getContact();
        $selected = $contact->getProducts();
        if ($selected != "" && !is_array($selected)) {
            $selected = explode('&',$selected);
        }
        return $selected;
    }

    public function getContact()
    {
        $contactId = $this->getRequest()->getParam('id');
        $contact   = $this->contactFactory->create();
        if ($contactId != "") {
            $contact->load($contactId);
        }
        return $contact;
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
