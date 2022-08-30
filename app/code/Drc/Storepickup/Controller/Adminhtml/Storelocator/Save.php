<?php
/**
 * {{Drc}}_{{Storepickup}} extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  {{Drc}}
 *                     @package   {{Drc}}_{{Storepickup}}
 *                     @copyright Copyright (c) {{2016}}
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Drc\Storepickup\Controller\Adminhtml\Storelocator;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Drc\Storepickup\Controller\Adminhtml\Storelocator
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Drc\Storepickup\Model\StorelocatorFactory $storelocatorFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Drc\Storepickup\Model\StorelocatorFactory $storelocatorFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->backendSession = $backendSession;
        parent::__construct($storelocatorFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('storelocator');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $storelocator = $this->initStorelocator();
            
            $imageRequest = $this->getRequest()->getFiles('image');
            if ($imageRequest) {
                if (isset($imageRequest['name'])) {
                    $fileName = $imageRequest['name'];
                } else {
                    $fileName = '';
                }
            } else {
                 $fileName = '';
            }
                
            if ($imageRequest && strlen($fileName)) {
                try {
                        $uploader = $this->_objectManager->
                        create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'image']);
                        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                        $imageAdapter = $this->_objectManager
                        ->get('Magento\Framework\Image\AdapterFactory')->create();
                        $uploader->addValidateCallback('image', $imageAdapter, 'validateUploadFile');
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(true);
                        $mediaDirectory = $this->_objectManager->
                        get('Magento\Framework\Filesystem')->getDirectoryRead(DirectoryList::MEDIA);
                        $config = $this->_objectManager->get('Magento\Catalog\Model\Product\Media\Config');
                        $pth = $mediaDirectory->getAbsolutePath('Storepickup/images');
                        $result = $uploader->save($mediaDirectory->getAbsolutePath('Storepickup/images'));
                        unset($result['tmp_name']);
                        unset($result['path']);
                        $data['image'] = 'Storepickup/images'.$result['file'];
                } catch (\Exception $e) {
                    $data['image'] = $fileName;
                }
            } elseif (isset($data['image']['delete'])) {
                if ($data['image']['value']) {
                    $om = \Magento\Framework\App\ObjectManager::getInstance();
                    $filesystem = $om->get('Magento\Framework\Filesystem');
                    $reader = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                    $deletePath = $reader->getAbsolutePath($data['image']['value']);
                    
                    if (file_exists($deletePath)) {
                        unlink($deletePath);
                    }
                    $data['image'] = '';
                }
            } else {
                if (isset($data['image']['value'])) {
                    $data['image'] = $data['image']['value'];
                }
            }
            
            $storelocator->setData($data);
            $this->_eventManager->dispatch(
                'drc_storepickup_storelocator_prepare_save',
                [
                    'storelocator' => $storelocator,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $storelocator->save();
                $this->messageManager->addSuccess(__('The Storelocator has been saved.'));
                $this->backendSession->setDrcStorepickupStorelocatorData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'drc_storepickup/*/edit',
                        [
                            'storelocator_id' => $storelocator->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('drc_storepickup/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Storelocator.'));
            }
            $this->_getSession()->setDrcStorepickupStorelocatorData($data);
            $resultRedirect->setPath(
                'drc_storepickup/*/edit',
                [
                    'storelocator_id' => $storelocator->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('drc_storepickup/*/');
        return $resultRedirect;
    }
}
