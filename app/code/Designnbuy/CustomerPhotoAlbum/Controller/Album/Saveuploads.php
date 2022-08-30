<?php
namespace Designnbuy\CustomerPhotoAlbum\Controller\Album;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResourceConnection;


class Saveuploads extends \Magento\Framework\App\Action\Action
{
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    private $resultJsonFactory;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Designnbuy\CustomerPhotoAlbum\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->helper = $helper;
        $this->resultJsonFactory=$resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {	
        $request = $this->getRequest()->getParams();
        $resultJson = $this->resultJsonFactory->create();
        $bgImage = $this->getRequest()->getFiles('albumfiles');
        
        if (isset($_FILES['albumfiles']['name'][0]) && !empty($_FILES['albumfiles']['name'][0])) {
            if (!file_exists($this->_mediaDirectory->getAbsolutePath('designnbuy/uploadedImage/'))) {
                mkdir($this->_mediaDirectory->getAbsolutePath('designnbuy/uploadedImage/'), 0777, true);           
            }
            if (!file_exists($this->_mediaDirectory->getAbsolutePath('designnbuy/temp/'))) {
                mkdir($this->_mediaDirectory->getAbsolutePath('designnbuy/temp/'), 0777, true);           
            }
            try{
                $files = array();
                $mediaPath = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                        ->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                for($i=0;$i<count($bgImage);$i++){
                    
                    $target = $this->_mediaDirectory->getAbsolutePath('designnbuy/temp/');
                        
                            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'albumfiles['.$i.']']);
                            $uploader->setAllowedExtensions(['jpg','png','jpeg']);
                            $uploader->setAllowRenameFiles(true);
                            $result = $uploader->save($target);
                            if(isset($result['file']) && $result['file'] != ""){
                                $files[] = $result['file'];
                            }
                }
                if(!empty($files)){
                    $encodeFiles = implode("|",$files);
                    if(isset($request['uploaded_files']) && $request['uploaded_files'] != ""){
                        $encodeFiles = $encodeFiles.'|'.$request['uploaded_files'];
                    }
                    if(isset($request['removed_files']) && $request['removed_files'] != ""){
                        $encodeFilesData = explode("|",$encodeFiles);
                        $requestd_files_data = explode("|",$request['removed_files']);
                        
                        $encodeFilesDiff = array_diff($encodeFilesData,$requestd_files_data);
                        $encodeFiles = implode("|",$encodeFilesDiff);
                    }
                    $listUrlFiles = [];
                    foreach (explode("|",$encodeFiles) as $urlFile) {
                        if($urlFile){
                            $listUrlFiles[] = $mediaPath.'designnbuy/temp/'.$urlFile;
                        }
                    }
                    if(!empty($listUrlFiles)){
                        $listUrlFiles = implode(",",$listUrlFiles);
                    }
                    return $resultJson->setData(
                        [
                            'uploaded' => true,
                            'message' => $encodeFiles,
                            'list_files' => $listUrlFiles
                        ]
                    );
                    
                } else {
                    return $resultJson->setData(
                        [
                            'uploaded' => false,
                            'message' => "No files available."
                        ]
                    );
                }
                
            } catch (\Exception $e) {
                return $resultJson->setData(
                    [
                        'uploaded' => false,
                        'message' => $e->getMessage()
                    ]
                );
            }
        } else {
            return $resultJson->setData(
                [
                    'uploaded' => false,
                    'message' => "Please upload valid files"
                ]
            );
        }
        
    }

}