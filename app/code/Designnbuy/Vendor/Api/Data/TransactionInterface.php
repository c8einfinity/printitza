<?php


namespace Designnbuy\Vendor\Api\Data;

interface TransactionInterface
{

    const TITLE = 'Title';
    const TRANSACTION_ID = 'transaction_id';


    /**
     * Get transaction_id
     * @return string|null
     */
    
    public function getTransactionId();

    /**
     * Set transaction_id
     * @param string $transaction_id
     * @return \Designnbuy\Vendor\Api\Data\TransactionInterface
     */
    
    public function setTransactionId($transactionId);

    /**
     * Get Title
     * @return string|null
     */
    
    public function getTitle();

    /**
     * Set Title
     * @param string $Title
     * @return \Designnbuy\Vendor\Api\Data\TransactionInterface
     */
    
    public function setTitle($Title);
}
