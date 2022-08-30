<?php
namespace Designnbuy\Commission\Ui\DataProvider\Redemption\Form;

use Designnbuy\Commission\Model\ResourceModel\Redemption\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class RedemptionDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Designnbuy\Commission\Model\ResourceModel\Redemption\Collection
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
     * @param CollectionFactory $redemptionCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $redemptionCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $redemptionCollectionFactory->create();
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
        /** @var $commission \Designnbuy\Commission\Model\Redemption */
        foreach ($items as $redemption) {
            $redemption = $redemption->load($redemption->getId()); //temporary fix
            $data = $redemption->getData();
            if($redemption->getUserType() == 1){

                $userData = $redemption->getUserInformation();
                $userInformation = $userData->getData();
                if($userInformation):
                    $user_record['email'] = $userInformation['email'];
                    /*Data retrive from designer*/
                    $resellerInfo = $redemption->getResellerInformation();
                    $resellerInformation = $resellerInfo->getData();
                    $user_record['bank_detail'] = $resellerInformation['bank_detail'];
                    $user_record['vat_number'] = $resellerInformation['vat_number'];
                    $data = array_merge($data, $user_record);
                endif;

            } else {
                if($redemption->getUserId()) {
                    /*Data retrive from customer*/
                    $customerData = $redemption->getCustomerInformation();
                    $customerInformation = $customerData->getData();
                    if($customerInformation):
                        $customer_record['email'] = $customerInformation['email'];
                        /*Data retrive from designer*/
                        $designerInfo = $redemption->getDesignerInformation();
                        $designerInformation = $designerInfo->getData();
                        $customer_record['bank_detail'] = $designerInformation['bank_detail'];
                        $customer_record['vat_number'] = $designerInformation['vat_number'];
                        $data = array_merge($data, $customer_record);
                    endif;
                }
            }

            $this->loadedData[$redemption->getId()] = $data;
            
        }
        
        $data = $this->dataPersistor->get('designer_redemption_form_data');

        if (!empty($data)) {
            $redemption = $this->collection->getNewEmptyItem();
            $redemption->setData($data);
            $this->loadedData[$redemption->getId()] = $redemption->getData();
            $this->dataPersistor->clear('designer_redemption_form_data');
        }
        return $this->loadedData;
    }
}
