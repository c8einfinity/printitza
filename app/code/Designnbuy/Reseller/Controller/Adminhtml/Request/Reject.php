<?php
/**
 * Designnbuy_Reseller extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Designnbuy
 * @package   Designnbuy_Reseller
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Designnbuy\Reseller\Controller\Adminhtml\Request;

class Reject extends \Designnbuy\Reseller\Controller\Adminhtml\Request
{
    /**
     * Request factory
     * 
     * @var \Designnbuy\Reseller\Api\Data\RequestInterfaceFactory
     */
    protected $requestFactory;

    /**
     * Data Object Processor
     * 
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * Data Object Helper
     * 
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * Data Persistor
     * 
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * constructor
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Designnbuy\Reseller\Api\Data\RequestInterfaceFactory $requestFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Designnbuy\Reseller\Api\Data\RequestInterfaceFactory $requestFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->requestFactory      = $requestFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper    = $dataObjectHelper;
        $this->dataPersistor       = $dataPersistor;
        parent::__construct($context, $coreRegistry, $requestRepository, $resultPageFactory);
    }

    /**
     * run the action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Designnbuy\Reseller\Api\Data\RequestInterface $request */
        $request = null;
        $postData =  $this->getRequest()->getParams();
        $data = $postData; 
        if($data['type'] == 'reject'){
            $data['status'] = 0;
        }
        $id = !empty($data['request_id']) ? $data['request_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $request = $this->requestRepository->getById((int)$id);
            } else {
                unset($data['request_id']);
                $request = $this->requestFactory->create();
            }
            $this->dataObjectHelper->populateWithArray($request, $data, \Designnbuy\Reseller\Api\Data\RequestInterface::class);
            $this->requestRepository->save($request);
            $this->messageManager->addSuccessMessage(__('You saved the Request'));
            $this->dataPersistor->clear('designnbuy_reseller_request');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('designnbuy_reseller/request/edit', ['request_id' => $request->getId()]);
            } else {
                $resultRedirect->setPath('designnbuy_reseller/request');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('designnbuy_reseller_request', $postData);
            $resultRedirect->setPath('designnbuy_reseller/request/edit', ['request_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Request'));
            $this->dataPersistor->set('designnbuy_reseller_request', $postData);
            $resultRedirect->setPath('designnbuy_reseller/request/edit', ['request_id' => $id]);
        }
        return $resultRedirect;
    }

}
