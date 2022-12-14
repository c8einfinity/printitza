<?php
namespace Designnbuy\Productattach\Controller\Adminhtml\Index;

/**
 * Class MassDelete
 * @package Designnbuy\Productattach\Controller\Adminhtml\Index
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $filter;

    /** 
     * @var \Designnbuy\Productattach\Model\ResourceModel\Productattach\CollectionFactory
     */
    private $collectionFactory;
    
    /**
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Designnbuy\Productattach\Model\ResourceModel\Productattach\CollectionFactory $collectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Designnbuy\Productattach\Model\ResourceModel\Productattach\CollectionFactory $collectionFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Designnbuy_Productattach::delete');
    }

    public function execute()
    {
        try {
            $logCollection = $this->filter->getCollection($this->collectionFactory->create());

            $itemsDeleted = 0;
            foreach ($logCollection as $item) {
                $item->delete();
                $itemsDeleted++;
            }
            $this->messageManager->addSuccess(__('A total of %1 Attachment(s) were deleted.', $itemsDeleted));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('productattach/index');
    }
}
