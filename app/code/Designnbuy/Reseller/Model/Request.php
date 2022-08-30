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
namespace Designnbuy\Reseller\Model;

/**
 * @method \Designnbuy\Reseller\Model\ResourceModel\Request getResource()
 * @method \Designnbuy\Reseller\Model\ResourceModel\Request _getResource()
 */
class Request extends \Magento\Framework\Model\AbstractModel implements \Designnbuy\Reseller\Api\Data\RequestInterface
{
    /**
     * Cache tag
     * 
     * @var string
     */
    const CACHE_TAG = 'designnbuy_reseller_request';

    /**
     * Cache tag
     * 
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Event prefix
     * 
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_reseller_request';

    /**
     * Event object
     * 
     * @var string
     */
    protected $_eventObject = 'request';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Designnbuy\Reseller\Model\ResourceModel\Request::class);
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
     * set First Name
     *
     * @param mixed $username
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     */
    public function setUsername($username)
    {
        return $this->setData(\Designnbuy\Reseller\Api\Data\RequestInterface::USERNAME, $username);
    }

    /**
     * get User Name
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getData(\Designnbuy\Reseller\Api\Data\RequestInterface::USERNAME);
    }


    /**
     * set First Name
     *
     * @param mixed $firstName
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     */
    public function setFirstName($firstName)
    {
        return $this->setData(\Designnbuy\Reseller\Api\Data\RequestInterface::FIRST_NAME, $firstName);
    }

    /**
     * get First Name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->getData(\Designnbuy\Reseller\Api\Data\RequestInterface::FIRST_NAME);
    }

    /**
     * set Last Name
     *
     * @param mixed $lastName
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     */
    public function setLastName($lastName)
    {
        return $this->setData(\Designnbuy\Reseller\Api\Data\RequestInterface::LAST_NAME, $lastName);
    }

    /**
     * get Last Name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->getData(\Designnbuy\Reseller\Api\Data\RequestInterface::LAST_NAME);
    }

    /**
     * set Email
     *
     * @param mixed $email
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     */
    public function setEmail($email)
    {
        return $this->setData(\Designnbuy\Reseller\Api\Data\RequestInterface::EMAIL, $email);
    }

    /**
     * get Email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getData(\Designnbuy\Reseller\Api\Data\RequestInterface::EMAIL);
    }

    /**
     * set Password
     *
     * @param mixed $password
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     */
    public function setPassword($password)
    {
        return $this->setData(\Designnbuy\Reseller\Api\Data\RequestInterface::PASSWORD, $password);
    }

    /**
     * get Password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->getData(\Designnbuy\Reseller\Api\Data\RequestInterface::PASSWORD);
    }

    /**
     * set Store Code
     *
     * @param mixed $storeCode
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     */
    public function setStoreCode($storeCode)
    {
        return $this->setData(\Designnbuy\Reseller\Api\Data\RequestInterface::STORE_CODE, $storeCode);
    }

    /**
     * get Store Code
     *
     * @return string
     */
    public function getCompanyRegistrationNumber()
    {
        return $this->getData(\Designnbuy\Reseller\Api\Data\RequestInterface::COMPANY_REGISTRATION_NUMBER);
    }



    public function setCompanyRegistrationNumber($companyRegistrationNumber)
    {
        return $this->setData(\Designnbuy\Reseller\Api\Data\RequestInterface::COMPANY_REGISTRATION_NUMBER, $companyRegistrationNumber);
    }

    /**
     * get Store Code
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->getData(\Designnbuy\Reseller\Api\Data\RequestInterface::STORE_CODE);
    }
    /**
     * set Request Status
     *
     * @param mixed $status
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     */
    public function setStatus($status)
    {
        return $this->setData(\Designnbuy\Reseller\Api\Data\RequestInterface::STATUS, $status);
    }

    /**
     * get Request Status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(\Designnbuy\Reseller\Api\Data\RequestInterface::STATUS);
    }
}
