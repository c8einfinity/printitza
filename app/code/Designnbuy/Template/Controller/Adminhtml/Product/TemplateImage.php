<?php

namespace Designnbuy\Template\Controller\Adminhtml\Product;

/**
 * Delete Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class TemplateImage extends \Magento\Backend\App\Action
{
    /**
     * Template Collection Factory.
     *
     * @var \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templateCollectionFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonResultFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Designnbuy\Template\Model\Template $templateCollectionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ) {
        parent::__construct($context);

        $this->_templateCollectionFactory = $templateCollectionFactory;
        $this->jsonResultFactory = $jsonResultFactory;
    }
    /**
     * Dispatch request
     */
    public function execute()
    {

        $template_id = $this->getRequest()->getParam('id');
        if($template_id){
            $templateCollection = $this->_templateCollectionFactory->load($template_id);
            $data = [];
            if(!empty($templateCollection->getData())){
                //$data['templateUrl'] = $this->getUrl('template/template/edit', array('id' => $template_id));
                if($templateCollection->getImage()){
                    $data['templateImage'] = '<fieldset class="admin__field template_image"><div class="admin__field-control admin__control-grouped"><div class="admin__field admin__field-large"><div class="admin__field-control"><img src="'.$templateCollection->getImage().'" height="150px" width="150px" alt="Template Image" /></div><a href="'.$this->getUrl('template/template/edit', array('id' => $template_id)).'" target="_blank">Go To Template</a></div></div></fieldset>';
                }
            } else {
                $data['error'] = __('Something went wrong while fetching the information.');
            }
        } else {
            $data['error'] = __('Something went wrong while fetching the information.');
        }
        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        return $result;

    }
}
