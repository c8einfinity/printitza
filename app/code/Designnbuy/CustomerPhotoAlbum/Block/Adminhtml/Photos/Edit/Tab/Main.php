<?php

namespace Designnbuy\CustomerPhotoAlbum\Block\Adminhtml\Photos\Edit\Tab;

/**
 * Photos edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Designnbuy\CustomerPhotoAlbum\Model\Status
     */
    protected $_status;
    /**
     * @var \Designnbuy\CustomerPhotoAlbum\Helper\Data
     */
    protected $_helper;
    /**
     * @var \Designnbuy\CustomerPhotoAlbum\Model\albumFactory
     */
    protected $_albumFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Designnbuy\CustomerPhotoAlbum\Model\Status $status,
        \Designnbuy\CustomerPhotoAlbum\Helper\Data $helper,
        \Designnbuy\CustomerPhotoAlbum\Model\Album $AlbumFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_status = $status;
        $this->_helper = $helper;
        $this->_albumFactory = $AlbumFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        /* @var $model \Designnbuy\CustomerPhotoAlbum\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('photos');

        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Item Information')]);

        if ($model->getId()) {
            $fieldset->addField('photo_id', 'hidden', ['name' => 'photo_id']);
        }

		
        $fieldset->addField(
            'album_id',
            'select',
            [
                'name' => 'album_id',
                'label' => __('Album Name'),
                'title' => __('Album Name'),
                'required' => true,
                'values' => $this->albumArray(),
				//'readonly' => true,
                'disabled' => $isElementDisabled
            ]
        );
					
        $fieldset->addType('required_image', 'Designnbuy\CustomerPhotoAlbum\Block\Adminhtml\Photos\Edit\Tab\Required');
        $fieldset->addField(
            'path',
            'required_image',
            [
                'name' => 'path',
                'label' => __('Image'),
                'title' => __('Image'),
				'disabled' => $isElementDisabled
            ]
        );

						

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }
        $model->setPath($this->_helper->getAdminImageUrl($model->getPath()));
        
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Item Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Item Information');
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
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getTargetOptionArray(){
    	return array(
    				'_self' => "Self",
					'_blank' => "New Page",
    				);
    }
    
    public function albumArray()
    {
        $collection = $this->_albumFactory->getCollection();
        $collection->addFieldToSelect("album_id");
        $collection->addFieldToSelect("title");
        $collection->addFieldToFilter("customer_id","999999999");
        $options = array();
        foreach($collection as $data){
            $options[$data->getAlbumId()] = $data->getTitle();
        }
        return $options;
    }
}
