<?php
/**
 * {{Drc}}_{{Storepickup}} extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  {{Drc}}
 *                     @package   {{Drc}}_{{Storepickup}}
 *                     @copyright Copyright (c) {{2016}}
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Drc\Storepickup\Model;

/**
 * @method Storelocator setStoreTitle($storeTitle)
 * @method Storelocator setAddress($address)
 * @method Storelocator setCity($city)
 * @method Storelocator setState($state)
 * @method Storelocator setPincode($pincode)
 * @method Storelocator setCountry($country)
 * @method Storelocator setPhone($phone)
 * @method Storelocator setEmail($email)
 * @method Storelocator setImage($image)
 * @method Storelocator setIsEnable($isEnable)
 * @method Storelocator setLatitude($latitude)
 * @method Storelocator setLongitude($longitude)
 * @method Storelocator setZoomLevel($zoomLevel)
 * @method mixed getStoreTitle()
 * @method mixed getAddress()
 * @method mixed getCity()
 * @method mixed getState()
 * @method mixed getPincode()
 * @method mixed getCountry()
 * @method mixed getPhone()
 * @method mixed getEmail()
 * @method mixed getImage()
 * @method mixed getIsEnable()
 * @method mixed getLatitude()
 * @method mixed getLongitude()
 * @method mixed getZoomLevel()
 * @method Storelocator setCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Storelocator setUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 */
class Storelocator extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'drc_storepickup_storelocator';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'drc_storepickup_storelocator';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'drc_storepickup_storelocator';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Drc\Storepickup\Model\ResourceModel\Storelocator');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
