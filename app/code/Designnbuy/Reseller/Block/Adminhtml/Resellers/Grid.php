<?php
namespace Designnbuy\Reseller\Block\Adminhtml\Resellers;

use Designnbuy\Reseller\Model\Config\Source\MassUserStatus;
use Designnbuy\Reseller\Model\Config\Source\MassStoreStatus;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $resellerModel;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\Reseller\Model\Resellers $resellerModel,
        \Magento\Framework\Module\Manager $moduleManager,
        \Designnbuy\Reseller\Helper\Data $resellerHelper,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->moduleManager = $moduleManager;
        $this->resellerModel = $resellerModel;
        $this->resellerHelper = $resellerHelper;
        $this->authSession = $authSession;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('resellerGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        //$this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
       
        $collection = $this->resellerModel->getCollection();
        $collection->getSelect()
                   ->joinLeft(["admin_user" => 'admin_user'],
                               "admin_user.user_id = main_table.user_id", ['*']
                             )
                   ->joinLeft(["store_website" => 'store_website'],
                               "store_website.website_id = main_table.website_id",
                               ['store_website.website_id','store_website.name', 'store_website.default_group_id']
                             )
                   ->joinLeft(["store_group" => 'store_group'],
                               "store_group.group_id = store_website.default_group_id",
                               ['store_group.default_store_id']
                             )
                   ->joinLeft(["store" => 'store'],
                               "store.store_id = store_group.default_store_id",
                              ['store.is_active as store_status']
                             );

        $collection->addFieldToFilter('email',['notnull' => true]);
        $collection->addFieldToFilter('store_website.website_id',['notnull' => true]);
        
        $collection->addFilterToMap('website_id', 'store_website.website_id');

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }


    protected function _prepareColumns()
    {
        $this->addColumn(
            'reseller_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'reseller_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'firstname',
            [
                'header' => __('First Name'),
                'index' => 'firstname',
            ]
        );

        $this->addColumn(
            'lastname',
            [
                'header' => __('Last Name'),
                'index' => 'lastname',
            ]
        );

        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email',
            ]
        );

        $this->addColumn(
            'website_id',
            [
                'header' => __('Website Name'),
                'index' => 'website_id',
                'type' => 'options',
                'options' => $this->websiteName()
            ]
        );

        $this->addColumn(
            'store_status',
            [
                'header' => __('Store Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status()
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header' => __('Edit'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit'
                        ],
                        'field' => 'reseller_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        $this->addColumn(
            'importproducts',
            [
                'header' => __('Import Products'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Import Products'),
                        'url' => [
                            'base' => '*/*/importproducts/import_id/4'
                        ],
                        'field' => 'reseller_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );

        return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $user = $this->authSession->getUser();
        $reseller = $this->resellerHelper->isResellerUser($user->getId());
        if(!$reseller):
            $this->setMassactionIdField('reseller_id');
            $this->getMassactionBlock()->setFormFieldName('reseller');
            
            $userStatuses = MassUserStatus::getAvailableStatuses();
            $storeStatuses = MassStoreStatus::getAvailableStatuses();
            $this->getMassactionBlock()->addItem(
                'userstatus',
                [
                    'label' => __('Change User Status'),
                    'url' => $this->getUrl('designnbuy_reseller/*/massUserStatus', ['_current' => true]),
                    'additional' => [
                        'visibility' => [
                            'name' => 'userstatus',
                            'type' => 'select',
                            'class' => 'required-entry',
                            'label' => __('Status'),
                            'values' => $userStatuses,
                        ],
                    ],
                ]
            );

            $this->getMassactionBlock()->addItem(
                'storestatus',
                [
                    'label' => __('Change Store Status'),
                    'url' => $this->getUrl('designnbuy_reseller/*/massStoreStatus', ['_current' => true]),
                    'additional' => [
                        'visibility' => [
                            'name' => 'storestatus',
                            'type' => 'select',
                            'class' => 'required-entry',
                            'label' => __('Status'),
                            'values' => $storeStatuses,
                        ],
                    ],
                ]
            );
            return $this;
        endif;
    }

    protected function status(){
        return ['1'=>__('Enabled'), '0' =>__('Disabled')];
    }

    protected function websiteName(){
        if(!empty($this->getPreparedCollection())){
            $websiteName = [];
            foreach($this->getPreparedCollection() as $data){
                $websiteName[$data->getWebsiteId()] = $data->getName();
            }
            return $websiteName;
        }
        return [];
    }

    public function getGridUrl()
    {
        return $this->getUrl('designnbuy_reseller/resellers', ['_current' => true]);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['reseller_id' => $row->getId()]
        );
    }
}
