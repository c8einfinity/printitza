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
namespace Designnbuy\Reseller\Api\Data;

/**
 * @api
 */
interface RequestInterface
{
    /**
     * Username attribute constant
     * 
     * @var string
     */
    const USERNAME = 'username';

    /**
     * First Name attribute constant
     * 
     * @var string
     */
    const FIRST_NAME = 'first_name';

    /**
     * Last Name attribute constant
     * 
     * @var string
     */
    const LAST_NAME = 'last_name';

    /**
     * Email attribute constant
     * 
     * @var string
     */
    const EMAIL = 'email';

    /**
     * Password attribute constant
     * 
     * @var string
     */
    const PASSWORD = 'password';

    /**
     * Store Code attribute constant
     * 
     * @var string
     */
    const STORE_CODE = 'store_code';


    const COMPANY_REGISTRATION_NUMBER = 'company_registration_number';

    /**
     * Request Status attribute constant
     * 
     * @var string
     */
    const STATUS = 'status';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get UserName
     *
     * @return mixed
     */
    public function getUsername();

    /**
     * Set User Name
     *
     * @param mixed $username
     * @return RequestInterface
     */
    public function setUsername($username);

    /**
     * Get First Name
     *
     * @return mixed
     */
    public function getFirstName();

    /**
     * Set First Name
     *
     * @param mixed $firstName
     * @return RequestInterface
     */
    public function setFirstName($firstName);

    /**
     * Get Last Name
     *
     * @return mixed
     */
    public function getLastName();

    /**
     * Set Last Name
     *
     * @param mixed $lastName
     * @return RequestInterface
     */
    public function setLastName($lastName);

    /**
     * Get Email
     *
     * @return mixed
     */
    public function getEmail();

    /**
     * Set Email
     *
     * @param mixed $email
     * @return RequestInterface
     */
    public function setEmail($email);

    /**
     * Get Password
     *
     * @return mixed
     */
    public function getPassword();

    /**
     * Set Password
     *
     * @param mixed $password
     * @return RequestInterface
     */
    public function setPassword($password);

    /**
     * Get Store Code
     *
     * @return mixed
     */
    public function getStoreCode();

    /**
     * Set Store Code
     *
     * @param mixed $storeCode
     * @return RequestInterface
     */
    public function setStoreCode($storeCode);

    /**
     * Get Request Status
     *
     * @return mixed
     */
    public function getStatus();

    public function getCompanyRegistrationNumber();

    /**
     * Set Request Status
     *
     * @param mixed $status
     * @return RequestInterface
     */
    public function setStatus($status);
    
    public function setCompanyRegistrationNumber($companyRegistrationNumber);
}
