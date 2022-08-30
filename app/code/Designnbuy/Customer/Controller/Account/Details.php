<?php

namespace Designnbuy\Customer\Controller\Account;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class Update
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Details extends Action
{
    const CUSTOM_CUSTOMER_ATTR = 'profile_image';


    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Customer repository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    protected $_corporateHelper;

    protected $_corporateModel;

    /**
     * Customer Helper Class
     *
     * @var \Designnbuy\Customer\Helper\Data
     */
    protected $_customerHelper;


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
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Designnbuy\Customer\Helper\Data $customerHelper
    ) {
        $this->_customerHelper = $customerHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    /*public function dispatch(RequestInterface $request)
    {
        if (!$this->getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }*/

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
        //ini_set('display_errors', 1);
        $response = $this->_customerHelper->getCustomerDetails();
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);

    }

    public function getProfileImage($profileImageValue)
    {
        $url = '';
        if($profileImageValue){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $fileSystem = $objectManager->get('\Magento\Framework\Filesystem');
            $mediaDir = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::PUB)->getAbsolutePath().'media/';

            if(file_exists($mediaDir.'customer/'.$profileImageValue)){
                $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                $url = $storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'customer/'.$profileImageValue;

            }
        }
        return $url;
    }

}
