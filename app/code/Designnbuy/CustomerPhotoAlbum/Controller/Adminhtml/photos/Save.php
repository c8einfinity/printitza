<?php
namespace Designnbuy\CustomerPhotoAlbum\Controller\Adminhtml\photos;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;


class Save extends \Magento\Backend\App\Action
{

    /**
     * Base helper
     *
     * @var helper
     */
    private $helper;

    /**
     * @param Action\Context $context
     */
    public function __construct(
        Action\Context $context,
        \Designnbuy\Base\Helper\Data $helper
    )
    {
        parent::__construct($context);
        $this->helper = $helper;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();


        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Designnbuy\CustomerPhotoAlbum\Model\Photos');
            $request = array();
            $id = $this->getRequest()->getParam('photo_id');
            if ($id) {
                $model->load($id);
                $request['imageId'] = $id;
            } else {
                $this->getRequest()->setParam('back',true);
            }
			
			try {
                if(isset($_FILES['path']['name']) && $_FILES['path']['name'] != ""){
                    $files['file'] = $_FILES['path'];
                } else {
                    $files = '';
                }
                
                $request['name'] = $model->getPath();
                $request['chunk'] = 0;
                $request['chunks'] = 1;
                $request['isFront'] = 0;
                $request['isUpload'] = 1;
                $request['toolType'] = "web2print";
                $request['cur_album_id'] = $data['album_id'];
                $request['form_key'] = $data['form_key'];

                $responseData = $this->helper->upload($request,$files);
                $response = json_decode($responseData, true);
                
                if (isset($response['status']) && $response['status'] == "success") {
                    $this->messageManager->addSuccess(__('The Photos has been saved.'));
                } else {
                    $this->messageManager->addError('Something went wrong while saving the Photos.');
                }
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['photo_id' => $response['id'], '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Photos.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['photo_id' => $this->getRequest()->getParam('photo_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}