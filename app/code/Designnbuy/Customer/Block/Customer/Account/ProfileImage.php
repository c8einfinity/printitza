<?php
/**
 * Profile Avatar
 * 
 * @author Slava Yurthev
 */
namespace Designnbuy\Customer\Block\Customer\Account;
use \Magento\Framework\View\Element\Template;
use \Magento\Framework\View\Element\Template\Context;
use \Magento\Customer\Model\Session;
use \Magento\Customer\Model\Customer;
class ProfileImage extends Template {
	protected $session;
	public function __construct(
			Context $context,
			Session $session,
			Customer $customer,
			array $data = []
		){
		$this->session = $session;
		$this->customer = $customer;
		parent::__construct($context, $data);
	}
	public function getCustomer($id = false){
		if($id){
			$this->customer->load($id);
		}
		elseif($this->session && $this->session->getData('customer_id')){
			$this->customer->load($this->session->getData('customer_id'));
		}
		return $this->customer;
	}
	public function getSession(){
		var_dump(dirname(dirname(dirname(__DIR__))));
		return $this->session;
	}




    public function getProfileImage()
    {

        $url = '';
        if($this->getCustomer()->getData('profile_image')){
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $fileSystem = $objectManager->get('\Magento\Framework\Filesystem');
            $mediaDir = $fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::PUB)->getAbsolutePath().'media/';

            if(file_exists($mediaDir.'customer/'.$this->getCustomer()->getData('profile_image'))){
                $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                $url = $storeManager->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'customer/'.$this->getCustomer()->getData('profile_image');

            }
        }

        $image = $this->getCustomer()->getData('profile_image');
        $imagePreview = [];
        if($url != ''){
            $imagePreview[] = [
                'name' => $image,
                'file' =>  $image,
                'url' => $url,
            ];
        }
        return json_encode($imagePreview);
    }
}