<?php


namespace Designnbuy\Workflow\Model\User;

use Designnbuy\Workflow\Model\ResourceModel\User\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\User\Model\UserFactory;
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    protected $collection;

    protected $dataPersistor;

    protected $loadedData;

    protected $_userFactory;

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
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->_userFactory = $userFactory;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
        foreach ($items as $model) {
            $user = $this->_userFactory->create()->load($model->getUserId());
            $user->unsPassword();
            $this->loadedData[$model->getId()] = array_merge($model->getData(), $user->getData());
            $this->loadedData[$model->getId()]['required'] = false;
        }

        $data = $this->dataPersistor->get('designnbuy_workflow_user');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $data = $model->getData();
            $data['required'] = false;
            $this->loadedData[$model->getId()] = $data;
            //$this->loadedData[$model->getId()]['do_we_hide_it'] = true;
            $this->dataPersistor->clear('designnbuy_workflow_user');
        }


        return $this->loadedData;
    }
}
