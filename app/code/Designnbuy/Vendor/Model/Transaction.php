<?php


namespace Designnbuy\Vendor\Model;

use Designnbuy\Vendor\Api\Data\TransactionInterface;

class Transaction extends \Magento\Framework\Model\AbstractModel implements TransactionInterface
{
    /**
     * @var string
     */
    protected $_eventObject = 'transaction';

    protected $_idFieldName = 'transaction_id';
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Vendor\Model\ResourceModel\Transaction');
    }

    /**
     * Get transaction_id
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }

    /**
     * Set transaction_id
     * @param string $transactionId
     * @return \Designnbuy\Vendor\Api\Data\TransactionInterface
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData(self::TRANSACTION_ID, $transactionId);
    }

    /**
     * Get Title
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set Title
     * @param string $Title
     * @return \Designnbuy\Vendor\Api\Data\TransactionInterface
     */
    public function setTitle($Title)
    {
        return $this->setData(self::TITLE, $Title);
    }
}
