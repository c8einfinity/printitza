<?php


namespace Designnbuy\Vendor\Model\User;

use Designnbuy\Vendor\Model\ResourceModel\User\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\User\Model\UserFactory;
use Magento\Catalog\Model\Locator\LocatorInterface;
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $collection;

    protected $dataPersistor;

    protected $loadedData;

    protected $_userFactory;

    protected $_transactionCollectionFactory;

    protected $_storeManager;

    /** @var PriceCurrencyInterface $priceCurrency */
    protected $priceCurrency;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        UserFactory $userFactory,
        \Designnbuy\Vendor\Helper\Data $vendorData,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Designnbuy\Vendor\Model\ResourceModel\Transaction\CollectionFactory $transactionCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->_userFactory = $userFactory;
        $this->_storeManager = $storeManager;
        $this->_transactionCollectionFactory = $transactionCollectionFactory;
        $this->vendorData = $vendorData;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        //echo "getCurrencySymbol".$this->_storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
        //echo $this->priceCurrency->convertAndFormat(10.00);
        $isVendor = false;
        if($this->vendorData->isVendor()){
            $isVendor = true;
        }
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $user = $this->_userFactory->create()->load($model->getUserId());
            $user->unsPassword();
            $this->loadedData[$model->getId()] = array_merge($model->getData(), $user->getData());
            $this->loadedData[$model->getId()]['required'] = false;
            $this->loadedData[$model->getId()]['is_vendor'] = $isVendor;
            $transactionCollection1 = $this->_transactionCollectionFactory->create();
            $transactionCollection1->addFieldToFilter('vendor_id',$model->getId());
            $transactionCollection1->addFieldToFilter('order_increment_id',['neq' => 'NULL']);
            $transactionCollection1->addExpressionFieldToSelect('credit_amount', 'SUM({{amount}})', 'amount');

            $transactionCollection2 = $this->_transactionCollectionFactory->create();
            $transactionCollection2->addFieldToFilter('vendor_id',$model->getId());
            $transactionCollection2->addFieldToFilter('order_increment_id',array('null' => true));
            $transactionCollection2->addExpressionFieldToSelect('debit_amount', 'SUM({{amount}})', 'amount');
            //$transactionCollection->addExpressionFieldToSelect('SUM(`amount`)', 'amount');
            $balanceAmount = $transactionCollection1->getFirstItem()->getCreditAmount() - $transactionCollection2->getFirstItem()->getDebitAmount();
            if($balanceAmount > 0){
                $this->loadedData[$model->getId()]['balance_amount'] = $balanceAmount;
            }

        }

        $data = $this->dataPersistor->get('designnbuy_vendor_user');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $data = $model->getData();
            $data['required'] = false;
            $data['is_vendor'] = $isVendor;

            $this->loadedData[$model->getId()] = $data;
            $this->dataPersistor->clear('designnbuy_vendor_user');
        }

        return $this->loadedData;
    }
}
