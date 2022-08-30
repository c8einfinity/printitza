<?php
/**
 * Copyright © 2019 Design 'N' Buy. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 * ALWAYS DO BETTER @a
 */
 
namespace Designnbuy\JobManagement\Ui\DataProvider\Jobmanagement\Form;

use Designnbuy\JobManagement\Model\ResourceModel\Jobmanagement\CollectionFactory;
use Designnbuy\JobManagement\Model\ResourceModel\Jobmanagement\JobCollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class DataProvider
 */

class JobDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Designnbuy\JobManagement\Model\ResourceModel\Jobmanagement\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    private $storeManager;
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Designnbuy\JobManagement\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Designnbuy\Base\Helper\Data
     */
    protected $_baseHelper;
    
    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_orderItemFactory;

    protected $_hotFolderHelper;

    protected $_urlInterface;
    
    /**
     * @var \Designnbuy\Workflow\Helper\Data
     */
    protected $workflowData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $jobCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        JobCollectionFactory $jobCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderInterface,
        \Designnbuy\JobManagement\Helper\Data $helper,
        \Designnbuy\Base\Helper\Data $baseHelper,
        \Designnbuy\HotFolder\Helper\Data $hotFolderHelper,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Designnbuy\JobManagement\Model\Url $url,
        \Magento\Framework\UrlInterface $urlInterface,
        \Designnbuy\Workflow\Helper\Data $workflowData,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $jobCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;
        $this->productFactory = $productFactory;
        $this->orderInterface = $orderInterface;
        $this->_helper = $helper;
        $this->_baseHelper = $baseHelper;
        $this->_hotFolderHelper = $hotFolderHelper;
        $this->_orderItemFactory = $orderItemFactory;
        $this->_url = $url;
        $this->_urlInterface = $urlInterface;
        $this->workflowData = $workflowData;
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
        /** @var $jobmanagement \Designnbuy\JobManagement\Model\Jobmanagement */
        $arrayData = array();
        foreach ($items as $jobmanagement) {
            $jobmanagement = $jobmanagement->load($jobmanagement->getId()); 
            $data = $jobmanagement->getData();

            $orderId = $jobmanagement->getOrderId();
            $order = $this->orderInterface->get($orderId);
            $productId = $jobmanagement->getProductId();
            $orderIncrementId = $order->getIncrementId();

            ## Download Output
            $itemId = $jobmanagement->getItemId();
            if($this->workflowData->getWorkflowUser()) {
                $workflowAccess = $this->checkWorflowUserAccess();    
                $arrayData['download_output'] = ($workflowAccess == true) ? "" : $this->_helper->getOutputDownloadUrl($orderIncrementId, $itemId);
            }else{
                $arrayData['download_output'] = $this->_helper->getOutputDownloadUrl($orderIncrementId, $itemId);    
            } 
            

            ## End Download Output

            ## VDP Output
            $_item = $this->_orderItemFactory->create()->load($itemId);
            
            $outputFolderPrefix = $this->_hotFolderHelper->outputFolderPrefix($_item->getProduct());
            $outputFolderPostfix = $this->_hotFolderHelper->outputFolderPostfix($_item->getProduct());
            $outputFolderMiddleName = $this->_hotFolderHelper->outputFolderMiddleName($order->getIncrementId(), $_item->getId(), $_item->getProduct());
            $zipName = '';
            if($outputFolderPrefix != '' || $outputFolderPostfix != '' || $outputFolderMiddleName != '') {
                $naming = false;
                if($outputFolderPrefix != ''){
                    $zipName .= $outputFolderPrefix.'_';
                }
                if($outputFolderMiddleName != ''){
                    $zipName .= $outputFolderMiddleName.'_';
                }
                if($outputFolderPostfix != ''){
                    $zipName .= $outputFolderPostfix;
                }
                $zipName .= ".zip";
                //$zipName = $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".zip";
            } else {
                $zipName = "order-".$order->getIncrementId()."-".$itemId.".zip";
            }

            $outputFilePath = $_item->getOutputFilePath();

            $outputDetails = json_decode($outputFilePath, true);

            if(!empty($outputDetails) && $outputDetails['folder_path']) {
                $folderLocation = $outputDetails['folder_path'];
            } else {
                $folderLocation = $this->_hotFolderHelper->outputFolderLocation($order->getIncrementId(), $_item->getProduct(), true);
            }
            if(isset($arrayData['download_output']) && $arrayData['download_output'] != ""){
                $arrayData['generate_output'] = $this->_urlInterface->getUrl('base/output/download') . "?file=".$zipName."&fl=".base64_encode($folderLocation)."&id=".$order->getEntityId()."&order_id=". $order->getIncrementId()."&item_id=". $_item->getId()."&product_id=". $_item->getProductId()."&generate_output=1";
            } else {
                $arrayData['generate_output'] = '';
            }
            
            $productOptions = $_item->getProductOptions();
            $VDPOutputArea = $this->_baseHelper->getVDPOutputArea();

            $outputFilePath = $_item->getOutputFilePath();
            $outputDetails = json_decode($outputFilePath, true);

            if(!empty($outputDetails) && $outputDetails['folder_path']) {
                $folderLocation = $outputDetails['folder_path'];
            } else {
                $folderLocation = $this->_hotFolderHelper->outputFolderLocation($orderIncrementId, $_item->getProduct(), true);
            }

            $outputFolderPrefix = $this->_hotFolderHelper->outputFolderPrefix($_item->getProduct());
            $outputFolderPostfix = $this->_hotFolderHelper->outputFolderPostfix($_item->getProduct());
            $outputFolderMiddleName = $this->_hotFolderHelper->outputFolderMiddleName($orderIncrementId, $_item->getId(), $_item->getProduct());
            
            $zipName = '';
            if($outputFolderPrefix != '' && $outputFolderPostfix != '' && $outputFolderMiddleName != '') {
                $zipName = "vdp_order-" . $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".zip";
            } else {
                $zipName = "vdp_order-".$_order->getIncrementId()."-".$_item->getId().".zip";
            }

            if(isset($productOptions['info_buyRequest']['vdp_file']) & $VDPOutputArea == 1):
                if($this->workflowData->getWorkflowUser()) {
                    $workflowAccess = $this->checkWorflowUserAccess();
                    $vdpUrl = ($workflowAccess == true) ? "" : $this->_urlInterface->getUrl('base/vdp/download', array('id' => $orderId, 'order_id' => $orderIncrementId, 'item_id' => $itemId, 'fl' => base64_encode($folderLocation), 'file' => $zipName));
                }else{
                    $vdpUrl = $this->_urlInterface->getUrl('base/vdp/download', array('id' => $orderId, 'order_id' => $orderIncrementId, 'item_id' => $itemId, 'fl' => base64_encode($folderLocation), 'file' => $zipName));   
                }
                //$vdpUrl = $this->_urlInterface->getUrl('base/vdp/download', array('id' => $orderId, 'order_id' => $orderIncrementId, 'item_id' => $itemId, 'fl' => base64_encode($folderLocation), 'file' => $zipName));
            else:
                //$vdpUrl = "";
                $vdpUrl = $this->_helper->getEmptyPath();
            endif; 
            $arrayData['vdp_output'] = $vdpUrl;
            ## End VDP Output
            
            ## Attachment 
            $attachementContent = array();
            if(isset($productOptions['info_buyRequest']['attachment']) && $productOptions['info_buyRequest']['attachment'] != ""):
                $attachementArray = $productOptions['info_buyRequest']['attachment']['fileName'];

                $fileUrl = "";
                foreach ($attachementArray as $attachement) {
                    //echo $attachement.'<br>';                    
                    $exploded_fileNname = explode(".",$attachement);                    
                    $fileType = $exploded_fileNname[1]; 
                    $currentFile = $this->getMediaUrl().$attachement;                   
                    switch (strtolower($fileType))
                    {
                        case 'jpg':
                        case 'png':
                        case 'jpeg':
                        case 'gif':
                        case 'ico':
                        case 'btm':
                           $fileUrl = $this->getMediaUrl().$attachement;
                        break;
                        
                        case 'svg':
                            $iconName = $this->getMediaUrl().'/icons/svg.svg';
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;

                        case 'cdr':
                            $iconName = '/icons/cdr.svg';
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;

                        case 'doc':
                        case 'docx':
                            $iconName = '/icons/doc.svg';
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;
                        
                        case 'csv':
                            $iconName = '/icons/csv.svg';
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;

                        case 'ps':
                            $iconName = '/icons/ps.svg';
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;

                        case 'swf':
                            $iconName = '/icons/swf.svg';
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;

                        case 'xls':
                        case 'xlsx':
                            $iconName = "icons/xls.svg";
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;
                        
                        case 'ppt':
                            $iconName = "icons/ppt.svg";
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;

                        case 'tgz':
                        case 'zip':
                            $iconName = "icons/zip.svg";
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;

                        case 'rar':
                            $iconName = "icons/rar.svg";
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;

                        case '7z':
                            $iconName = "icons/7z.svg";
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;

                        case 'txt':
                            $iconName = "icons/txt.svg";
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;

                        case 'pdfA':
                            $iconName = '/icons/pdf.svg';
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;
                        case 'ai':
                        case 'psd':
                        case 'pdf':
                            $iconName = "icons/pdf.svg";
                            $fileUrl = $this->getMediaUrl().$iconName;
                            break;
                        break;
                        default:
                            $iconName = '/icons/unknown.svg';
                            $fileUrl = $this->getMediaUrl().$iconName;
                        break;
                    }
                    $attachementContent['attachment'][] = [
                        'name' => $attachement,
                        'url' => $fileUrl,
                        'currentFile' => $currentFile,
                    ];
                    $data = array_merge($data, $attachementContent);
                    $this->loadedData[$jobmanagement->getId()] = $data; 
                }
            endif;

            if(sizeof($attachementContent) <= 0):
                $attachementContent['attachment'][] = [
                        'name' => 'Not Available',
                        'url' => $this->getMediaUrl().'not-available.png',
                    ];
                $data = array_merge($data, $attachementContent);
                $this->loadedData[$jobmanagement->getId()] = $data;
            endif;
            
            ## End Attachment 
            
            $arrayData['order_id_edit'] = $orderIncrementId;
            $_product = $this->productFactory->create()->load($productId);
            $arrayData['product_id_edit'] = $_product->getName();
            $data = array_merge($data, $arrayData);
            
            $this->loadedData[$jobmanagement->getId()] = $data;
        }

        $data = $this->dataPersistor->get('jobmanagement_jobmanagement_form_data');
        if (!empty($data)) {
            $jobmanagement = $this->collection->getNewEmptyItem();
            $jobmanagement->setData($data);
            $this->loadedData[$jobmanagement->getId()] = $jobmanagement->getData();
            $this->dataPersistor->clear('jobmanagement_jobmanagement_form_data');
        }
        //$this->loadedData[$jobmanagement->getId()]['do_we_hide_it'] = true;
        //$this->loadedData[$jobmanagement->getId()]['download_output'] = "";
        return $this->loadedData;
    }

    public function checkWorflowUserAccess() {
        $workFlowRole = $this->workflowData->getWorkflowUserRole();
        $accesses = $workFlowRole->getAccesses();
        if(isset($accesses) && !empty($accesses) && !in_array('download_files',$accesses)) {
            return true;
        }else {
            return false;
        }
    }

    public function getMediaUrl() {
        $mediaUrl = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'designnbuy/orderattachment/';
        return $mediaUrl;
    }
}
