<?php

namespace Designnbuy\Customer\Controller\Profile;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Model\FileProcessor;
use Magento\Customer\Model\FileProcessorFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\CustomerFactory as CustomerResourceFactory;

/**
 * Class Update
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Save extends Action
{
    const CUSTOM_CUSTOMER_ATTR = 'profile_image';

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \Magento\Backend\Model\Url
     */
    private $backendUrl;

    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @var FileProcessorFactory
     * @deprecated 101.0.0
     */
    protected $fileProcessorFactory;

    /**
     * Default attribute entity type code
     *
     * @var string
     */
    protected $_entityTypeCode = 'customer';

    /**
     * @var Customer
     */
    protected $customerModel;

    /**
     * @var CustomerResourceFactory
     */
    protected $customerResourceFactory;

    /**
     * Update constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Backend\Model\Url $backendUrl
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Customer\Model\Session $customerSession,
        CustomerInterfaceFactory $customerDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Backend\Model\Url $backendUrl,
        \Magento\Customer\Model\FileProcessorFactory $fileProcessorFactory = null,
        CustomerResourceFactory $customerResourceFactory,
        Customer $customerModel
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerDataFactory = $customerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->backendUrl = $backendUrl;
        $this->fileProcessorFactory = $fileProcessorFactory ?: ObjectManager::getInstance()
            ->get(\Magento\Customer\Model\FileProcessorFactory::class);
        $this->fileProcessor = $this->fileProcessorFactory->create(['entityTypeCode' => $this->_entityTypeCode]);
        $this->customerResourceFactory = $customerResourceFactory;
        $this->customerModel = $customerModel;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Retrieve customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    private function getSession()
    {
        return $this->customerSession;
    }

    /**
     * Render View
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $redirectUrl = null;
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('customer/account/');
        }

        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setUrl(
                $this->_redirect->error($this->_url->getUrl('customer/account/'))
            );
        }

        try {
            $profileImage = $this->getRequest()->getParam('profile_image');
            if($profileImage != ''){
                $result = $this->getFileProcessor()->moveTemporaryFile($profileImage);
                //$result = '/l/o/log_7.png';
                $customerId = $this->getSession()->getCustomerId();


                $customerNew = $this->customerModel->load($customerId);
                $customerData = $customerNew->getDataModel();
                $customerData->setCustomAttribute(self::CUSTOM_CUSTOMER_ATTR, $result);
                $customerNew->updateData($customerData);

                $customerResource = $this->customerResourceFactory->create();
                $customerResource->saveAttribute($customerNew, self::CUSTOM_CUSTOMER_ATTR);
            } else {
                $customerId = $this->getSession()->getCustomerId();


                $customerNew = $this->customerModel->load($customerId);
                $customerData = $customerNew->getDataModel();
                $customerData->setCustomAttribute(self::CUSTOM_CUSTOMER_ATTR, '');
                $customerNew->updateData($customerData);

                $customerResource = $this->customerResourceFactory->create();
                $customerResource->saveAttribute($customerNew, self::CUSTOM_CUSTOMER_ATTR);
            }

            $this->messageManager->addSuccess(__('You are successfully save the data!!'));

            $url = $this->_url->getUrl('customer/account/', ['_secure' => true]);

            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));
        } catch (\Exception $e) {
            $redirectUrl = $this->_url->getUrl('customer/account/');
            $this->messageManager->addException($e, __('We can\'t update the profile image.'));
        }

        $url = $redirectUrl;

        if (!$url) {
            $url = $this->_url->getUrl('customer/account/');
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->error($url));
    }

    /**
     * Set ignore_validation_flag to skip unnecessary address and customer validation
     *
     * @param Customer $customer
     * @return void
     */
    private function setIgnoreValidationFlag($customer)
    {
        $customer->setData('ignore_validation_flag', true);
    }


    /**
     * Get file processor
     *
     * @return FileProcessor
     * @deprecated 100.1.3
     */
    protected function getFileProcessor()
    {
        return $this->fileProcessor;
    }

}
