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

class InlineEdit extends \Designnbuy\Reseller\Controller\Adminhtml\Request
{
    /**
     * Core registry
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Request repository
     * 
     * @var \Designnbuy\Reseller\Api\RequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * Page factory
     * 
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Data object processor
     * 
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * Data object helper
     * 
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * JSON Factory
     * 
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * Request resource model
     * 
     * @var \Designnbuy\Reseller\Model\ResourceModel\Request
     */
    protected $requestResourceModel;

    /**
     * constructor
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Designnbuy\Reseller\Model\ResourceModel\Request $requestResourceModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Designnbuy\Reseller\Model\ResourceModel\Request $requestResourceModel
    ) {
        $this->dataObjectProcessor  = $dataObjectProcessor;
        $this->dataObjectHelper     = $dataObjectHelper;
        $this->jsonFactory          = $jsonFactory;
        $this->requestResourceModel = $requestResourceModel;
        parent::__construct($context, $coreRegistry, $requestRepository, $resultPageFactory);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $requestId) {
            /** @var \Designnbuy\Reseller\Model\Request|\Designnbuy\Reseller\Api\Data\RequestInterface $request */
            $request = $this->requestRepository->getById((int)$requestId);
            try {
                $requestData = $postItems[$requestId];
                $this->dataObjectHelper->populateWithArray($request, $requestData, \Designnbuy\Reseller\Api\Data\RequestInterface::class);
                $this->requestResourceModel->saveAttribute($request, array_keys($requestData));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithRequestId($request, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithRequestId($request, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithRequestId(
                    $request,
                    __('Something went wrong while saving the Request.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * Add Request id to error message
     *
     * @param \Designnbuy\Reseller\Api\Data\RequestInterface $request
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithRequestId(\Designnbuy\Reseller\Api\Data\RequestInterface $request, $errorText)
    {
        return '[Request ID: ' . $request->getId() . '] ' . $errorText;
    }
}
