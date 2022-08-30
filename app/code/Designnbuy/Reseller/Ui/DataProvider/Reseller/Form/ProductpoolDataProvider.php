<?php
namespace Designnbuy\Reseller\Ui\DataProvider\Reseller\Form;

use Designnbuy\Reseller\Model\ResourceModel\Productpool\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class ProductpoolDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Designnbuy\Designer\Model\ResourceModel\Level\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $productpoolCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $productpoolCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $productpoolCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $productpool \Designnbuy\Reseller\Model\Productpool */
        foreach ($items as $productpool) {
            $productpool = $productpool->load($productpool->getId()); //temporary fix
            $data = $productpool->getData();

            /* Prepare related products */
            $collection = $productpool->getProductpoolProducts()->addAttributeToSelect('name');
            $items = [];
            foreach ($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                ];
            }
            $data['data']['links']['product'] = $items;

            /* Set data */
            $this->loadedData[$productpool->getId()] = $data;
        }

        $data = $this->dataPersistor->get('reseller_productpool_form_data');
        if (!empty($data)) {
            $productpool = $this->collection->getNewEmptyItem();
            $productpool->setData($data);
            $this->loadedData[$productpool->getId()] = $productpool->getData();
            $this->dataPersistor->clear('reseller_productpool_form_data');
        }

        return $this->loadedData;
    }
}
