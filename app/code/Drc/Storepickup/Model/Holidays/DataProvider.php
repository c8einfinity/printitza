<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 * @author ALWAYS DO BETTER @a
 */

namespace Drc\Storepickup\Model\Holidays;

use Magento\Checkout\Model\ConfigProviderInterface;

class DataProvider implements ConfigProviderInterface
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context     
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    const ENABLED = 1;

    const SELF_ALL_STORE = 0;

    protected $_holidayCollectionFacroty;
    
    protected $_timezone;

    protected $_storeManager;
    
    protected $scopeConfig;

    public function __construct(
        \Magento\Store\Model\System\Store $systemStore,
        \Drc\Storepickup\Model\ResourceModel\Holidays\CollectionFactory $holidayCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_systemStore = $systemStore;
        $this->_holidayCollectionFacroty = $holidayCollectionFactory;        
        $this->_timezone = $timezone;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function getHolidayCollection() {
        $storeId = $this->_storeManager->getStore()->getId();      
        $collection = $this->_holidayCollectionFacroty->create(); 
        $collection = $collection->addStoreFilter();       
        $collection->addFieldToFilter(
            array('store_id'),
                array(array('in', array($storeId, SELF::SELF_ALL_STORE))
            )
        );
        $collection->addFieldToFilter('is_enable', SELF::ENABLED);
        return $collection;
    }
   
    public function getConfig()
    {
        $collection = $this->getHolidayCollection();
        $holidayContent = [];
        $holidaysEachYear = [];
        foreach ($collection as $key => $holiday) {
            $holidaysDateFrom = $this->_timezone->date(new \DateTime($holiday['from_date']))->format('d-m-yy');
            $holidaysDateTo = $this->_timezone->date(new \DateTime($holiday['to_date']))->format('d-m-yy');

            if ($holiday['holiday_type'] == 'each_year') {
                $holidaysEachYear = array_merge($holidaysEachYear,$this->getDatesFromRange($holidaysDateFrom, $holidaysDateTo, $format = 'd-m'));
            }else{
                $holidayContent = array_merge($holidayContent,$this->getDatesFromRange($holidaysDateFrom, $holidaysDateTo, $format = 'd-m-yy'));
            }
        }
        $config = [];
        $config['holidaysData'] = array_unique($holidayContent);
        $config['holidaysEachYearData'] = array_unique($holidaysEachYear);
        $config['productionTime'] = $this->scopeConfig->getValue('carriers/storepickup/production_time', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $config;
    }

    public function getDatesFromRange($start, $end, $format = 'Y-m-d') { 
        // Declare an empty array 
        $array = array();        
        $interval = new \DateInterval('P1D');
        $realEnd = new \DateTime($end); 
        $realEnd->add($interval); 
        $period = new \DatePeriod(new \DateTime($start), $interval, $realEnd); 
        // Use loop to store date into array 
        foreach($period as $date) {
            $array[] = $date->format($format);  
        }       
        // Return the array elements 
        return $array; 
    } 
}