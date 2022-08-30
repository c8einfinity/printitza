<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Product;

/**
 * Delete Designidea action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class Designideaimage extends \Magento\Backend\App\Action
{
    /**
     * Designidea Collection Factory.
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory
     */
    protected $_designideaCollectionFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonResultFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Designnbuy\Designidea\Model\Designidea $designideaCollectionFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
    ) {
        parent::__construct($context);

        $this->_designideaCollectionFactory = $designideaCollectionFactory;
        $this->jsonResultFactory = $jsonResultFactory;
    }
    /**
     * Dispatch request
     */
    public function execute()
    {
        $designidea_id = $this->getRequest()->getParam('id');
        if($designidea_id){
            $designideaCollection = $this->_designideaCollectionFactory->load($designidea_id);
            $data = [];
            if(!empty($designideaCollection->getData())){
                
                if($designideaCollection->getImage()){
                    $data['designideaImage'] = '<fieldset class="admin__field template_image"><div class="admin__field-control admin__control-grouped"><div class="admin__field admin__field-large"><div class="admin__field-control"><img src="'.$designideaCollection->getImage().'" height="150px" width="150px" alt="Designidea Image" /></div><a href="'.$this->getUrl('designidea/designidea/edit', array('id' => $designidea_id)).'" target="_blank">Go To Editable Artwork</a></div></div></fieldset>';
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
