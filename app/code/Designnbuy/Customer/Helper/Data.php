<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer Data Helper
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Customer\Helper;

class Data
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_frontendUrlBuilder;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Designnbuy\Customer\Model\ImageFactory
     */
    protected $_customerImageFactory;

    /**
     * @var \Designnbuy\Customer\Model\ResourceModel\Image\CollectionFactory
     */
    protected $_customerImageCollectionFactory;

    /**
     * @var \Designnbuy\Base\Helper\Output
     */
    protected $_dnbOutputHelper;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    protected $_corporateHelper;

    protected $_corporateModel;

    const CUSTOM_CUSTOMER_ATTR = 'profile_image';

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
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * @param \Magento\Framework\UrlInterface $frontendUrlBuilder
     */
    public function __construct(
        \Magento\Framework\UrlInterface $frontendUrlBuilder,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Designnbuy\Customer\Model\ImageFactory $customerImageFactory,
        \Designnbuy\Customer\Model\ResourceModel\Image\CollectionFactory $customerImageCollectionFactory,
        \Designnbuy\Base\Helper\Output $dnbOutputHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Backend\Model\UrlInterface $backendUrl
    )
    {
        $this->_frontendUrlBuilder = $frontendUrlBuilder;
        $this->_customerSession = $customerSession->create();
        $this->_customerImageFactory = $customerImageFactory;
        $this->_customerImageCollectionFactory = $customerImageCollectionFactory;
        $this->_dnbOutputHelper = $dnbOutputHelper;
        $this->customerRepository = $customerRepository;
        $this->addressFactory = $addressFactory;
        $this->moduleManager = $moduleManager;
        $this->backendUrl = $backendUrl;

    }

    /**
     * Retrieve subsription confirmation url
     *
     * @param \Designnbuy\Customer\Model\Design $design
     * @return string
     */
    public function getConfirmationUrl($design)
    {
        return $this->_frontendUrlBuilder->setScope(
            $design->getStoreId()
        )->getUrl(
            'customer/design/confirm',
            ['id' => $design->getId(), 'code' => $design->getCode(), '_nosid' => true]
        );
    }

    /**
     * Retrieve unsubsription url
     *
     * @param \Designnbuy\Customer\Model\Design $design
     * @return string
     */
    public function getUnsubscribeUrl($design)
    {
        return $this->_frontendUrlBuilder->setScope(
            $design->getStoreId()
        )->getUrl(
            'customer/design/unsubscribe',
            ['id' => $design->getId(), 'code' => $design->getCode(), '_nosid' => true]
        );
    }

    /**
     * Retrieve customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
    }


    public function saveUserImage($imageName,$isHD=null,$imageId=null)
    {
        $session = $this->_getSession();
        if($session->isLoggedIn() && $imageName != '')
        {
            $customer_id = $session->getCustomerId();
            $data['customer_id'] = $customer_id;
            $data['image'] = $imageName;
            $image = $this->_customerImageFactory->create();
            if($isHD){
                $image->load($imageId);
                $image->setHdImage($imageName);
                $image->save();
                return $image->getImageId();
            }else{
                $file = pathinfo($imageName);
                if(is_array($file) && !empty($file) && $file['extension'] != 'zip'){
                    $data['imgname'] = $imageName;
                    $image->setData($data);
                    $image->save();
                    return $image->getImageId();
                }
            }
        }
    }

    public function getUserImages(){
        $session = $this->_getSession();
        $userImages = array();
        $objectManager = $this->getObjectManager();
        $imageUrl = $objectManager->get('\Designnbuy\Base\Helper\Data')->getCustomerImageUrl();
        if($session->isLoggedIn())
        {
            $customerId = $session->getCustomerId();
            $collection = $this->_customerImageCollectionFactory->create();
            $collection->addFieldToFilter('customer_id',$customerId);
            foreach($collection as $key => $image){
                $userImages[$key]['id'] = $image->getImageId();
                $userImages[$key]['imageUrl'] = $imageUrl.$image->getImage();
                $userImages[$key]['vectorname'] = $image->getHdImage() != null ? $image->getHdImage() : '';
            }
        }
        return $userImages;
    }

    public function removeImage($request)
    {
        $objectManager = $this->getObjectManager();
        $imageDir = $objectManager->get('\Designnbuy\Base\Helper\Data')->getCustomerImageDir();
        if(isset($request['userImageId'])){
            $imageId = $request['userImageId'];
            $image = $this->_customerImageFactory->create();
            $image->load($imageId);

            if($image->getImage() != ''){
                $this->_dnbOutputHelper->deleteFile($imageDir.$image->getImage());
            }
            if($image->getHdImage() != ''){
                $this->_dnbOutputHelper->deleteFile($imageDir.$image->getHdImage());
            }
            $image->delete();
        } else {
            if(isset($request['userImageName'])){
                $imageName = $request['userImageName'];
                $this->_dnbOutputHelper->deleteFile($imageDir.$imageName);
            }
        }

    }

    protected function getObjectManager()
    {
        return $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * Render View
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function getCustomerDetails()
    {
        $response = [
            'error' => false,
            'status' => ''
        ];
        $customerData = [];
        if($this->_customerSession->isLoggedIn()) {
            $customerId = $this->_customerSession->getCustomerId();
            $customer = $this->customerRepository->getById($customerId);

            $customerData['email'] = $customer->getEmail();
            $customerData['prefix'] = $customer->getPrefix();
            $customerData['suffix'] = $customer->getSuffix();
            $customerData['dob'] = $customer->getDob();
            $customerData['firstname'] = $customer->getFirstname();
            $customerData['middlename'] = $customer->getMiddlename();
            $customerData['lastname'] = $customer->getLastname();
            $customerData['company'] = '';
            $customerData['street'] = '';
            $customerData['city'] = '';
            $customerData['region'] = '';
            $customerData['country'] = '';
            $customerData['postcode'] = '';
            $customerData['telephone'] = '';
            $customerData['vat'] = '';
            $customerData['profile_image'] = '';
            $customerData['corporate_logo'] = '';

            $profileImageUrl = '';

            if ($this->moduleManager->isEnabled('Designnbuy_Corporate')) {
                if ($this->_corporateHelper->isB2BWebsite()){
                    $websiteId = $this->_corporateHelper->getWebsiteId();
                    $corporate =  $this->_corporateModel->create()->getCorporateByWebsiteId($websiteId);
                    if($corporate->getLogo() != '' && $corporate->getCorporateLogo()){
                        $customerData['profile_image'] = $corporate->getCorporateLogo();
                        $customerData['corporate_logo'] = $corporate->getCorporateLogo();
                    }
                }
            }

            $customerCustomAttributes = $customer->getCustomAttributes();
            if(array_key_exists('profile_image', $customerCustomAttributes)){
                $profileImage = $customerCustomAttributes['profile_image'];
                $profileImageValue = $profileImage->getValue();
                $profileImageUrl = $this->getProfileImage($profileImageValue);
                $customerData['profile_image'] = $profileImageUrl;
            }


            if ($defaultBilling = $customer->getDefaultBilling()) {
                $address = $this->addressFactory->create();
                $address->load($defaultBilling);

                if ($address) {
                    $customerData['firstname'] = $address->getFirstname();
                    $customerData['middlename'] = $address->getMiddlename();
                    $customerData['lastname'] = $address->getLastname();
                    $customerData['street'] = $address->getStreetFull();
                    $customerData['city'] = $address->getCity();
                    $customerData['region'] = $address->getRegion();
                    $customerData['country'] = $address->getCountryModel()->getName();
                    $customerData['postcode'] = $address->getPostcode();
                    $customerData['telephone'] = $address->getTelephone();
                    $customerData['fax'] = $address->getFax();
                    $customerData['company'] = $address->getCompany();
                    $customerData['vat'] = $address->getVatId();
                }
            } else if ($defaultShipping = $customer->getDefaultShipping()) {
                $address = $this->addressFactory->create();
                $address->load($defaultShipping);
                if ($address) {
                    $customerData['firstname'] = $address->getFirstname();
                    $customerData['middlename'] = $address->getMiddlename();
                    $customerData['lastname'] = $address->getLastname();
                    $customerData['street'] = $address->getStreetFull();
                    $customerData['city'] = $address->getCity();
                    $customerData['region'] = $address->getRegion();
                    $customerData['country'] = $address->getCountryModel()->getName();
                    $customerData['postcode'] = $address->getPostcode();
                    $customerData['telephone'] = $address->getTelephone();
                    $customerData['fax'] = $address->getFax();
                    $customerData['company'] = $address->getCompany();
                    $customerData['vat'] = $address->getVatId();
                }

            }

            $response['id'] = $customerId;
            $response['error'] = 'false';
            $response['status'] = 'true';
            $response['data'] = $customerData;
        } else {
            $response['status'] = 'false';
            $response['data'] = [];
        }
        return $response;

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
    public function getProductsGridUrl()
    {
        return $this->backendUrl->getUrl('designnbuy_customer/group/products', ['_current' => true]);
    }
    
    public function getTemplateGridUrl()
    {
        return $this->backendUrl->getUrl('designnbuy_customer/group/templates', ['_current' => true]);
    }
    public function getDesignideasGridUrl()
    {
        return $this->backendUrl->getUrl('designnbuy_customer/group/designideas', ['_current' => true]);
    }
}
