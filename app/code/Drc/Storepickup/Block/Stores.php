<?php
/**
 * @author Drc Systems India Pvt Ltd.
*/

namespace Drc\Storepickup\Block;

class Stores extends \Magento\Framework\View\Element\Template
{
    
    protected function _getStoreCollection()
    {
                        
        $objectManager      = \Magento\Framework\App\ObjectManager::getInstance();
        $storeCollection    = $objectManager->
                              create('Drc\Storepickup\Model\ResourceModel\Storelocator\CollectionFactory');
        $collection         = $storeCollection->create()->load();
                        
        return $collection;
    }
    
    /**
     * Retrieve prepared stores collection
     *
     * @return Drc_StoreLocator_Model_ResourceModel_Locations_Collection
     */
    public function getStoreCollection()
    {
        //search by country state city
        $city = $this->getCities();
        $cityTmp = [];
        foreach ($city as $_city) {
            $cityTmp[] = $_city;
        }
        
        $data = $this->getRequest()->getPost();
        $country = '';
        $state = '';
        $city = $cityTmp[0]->getCity();
        $store = '';
        if (!empty($data)) {
            if (isset($data['country'])) {
                $country = $data['country'];
            }
            
            if (isset($data['state'])) {
                $state = $data['state'];
            }
            
            if (isset($data['city_select'])) {
                $city = $data['city_select'];
            }
        }
        
        if (is_null($this->_storesCollection)) {
            $this->_storesCollection = $this->_getStoreCollection();
            $this->_storesCollection->prepareForList($this->getCurrentPage());
            if (!empty($country)) {
                $this->_storesCollection->addFieldToFilter('country', ['like'=>$country]);
            }
            
            //search by state
            if (!empty($state)) {
                $this->_storesCollection->addFieldToFilter('state', ['like'=>$state]);
            }
            
            //search by city
            if (!empty($city)) {
                $this->_storesCollection->addFieldToFilter('city', ['like'=>$city]);
            }
        }
        return $this->_storesCollection;
    }
    
    public function getCities()
    {
        //get city
        return $this->_getStoreCollection()->addFieldToSelect('city')->distinct(true);
    }
    
    public function getCountries()
    {
        //get country
        return $this->_getStoreCollection()->addFieldToSelect('country')->distinct(true);
    }
    
    public function getStore()
    {
        //get store data
        $data = $this->getRequest()->getPost();
        $city = '';
        $this->_storesCollection = $this->_getStoreCollection();
        if (!empty($data)) {
            if (isset($data['city_select'])) {
                $city = $data['city_select'];
            }
        }
        if (!empty($city)) {
                $this->_storesCollection->addFieldToFilter('city', ['like'=>$city]);
        }
        return $this->_storesCollection->addFieldToSelect('store_title');
    }
}
