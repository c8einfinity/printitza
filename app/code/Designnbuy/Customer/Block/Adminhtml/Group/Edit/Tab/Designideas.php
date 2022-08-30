<?php
namespace Designnbuy\Customer\Block\Adminhtml\Group\Edit\Tab;

use \Designnbuy\Customer\Model\GroupFactory;

class Designideas extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory
     */
    private $designideaCollectionFactory;

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
     * Designideas constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param GroupFactory $contactFactory
     * @param \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory
     * @param \Designnbuy\Customer\Model\Group $attachModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $registry,
        GroupFactory $contactFactory,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory,
        \Designnbuy\Productattach\Model\Productattach $attachModel,
        array $data = []
    ) {
        $this->contactFactory = $contactFactory;
        $this->designideaCollectionFactory = $designideaCollectionFactory;
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
        $this->setId('designideasGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id') != "") {
            $this->setDefaultFilter(['in_designideas' => 1]);
        }
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    public function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_designideas') {
            
            $productIds = $this->_getSelectedDesignideas();
            
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
        $collection = $this->designideaCollectionFactory->create();
        $collection->addAttributeToSelect('title');
        
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
            'in_designideas',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_designideas',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedDesignideas(),
            ]
        );

        $this->addColumn(
            'entity_id',
            [
                'header' => __('Editable Artwork ID'),
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
                'class' => 'xxx',
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
        return $this->getUrl('*/*/designideasgrid', ['_current' => true]);
    }

    /**
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }

    public function _getSelectedDesignideas()
    {
        $contact = $this->getContact();
        $selected = $contact->getDesignidea();
        
        if ($selected != "" && !is_array($selected)) {
            $selected = explode('&',$selected);
        }
        return $selected;
    }

    /**
     * Retrieve selected designideas
     *
     * @return array
     */
    public function getSelectedDesignideas()
    {
        $contact = $this->getContact();
        $selected = $contact->getDesignidea();
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
