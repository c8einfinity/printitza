<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Controller\Adminhtml\Upload\Image;

use Magento\Framework\Controller\ResultFactory;

/**
 * Background image upload controller
 */
abstract class Backgroundaction extends \Magento\Backend\App\Action
{
    public $imageUploader;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Designnbuy\Base\Model\FileUploader $imageUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
    }
    /**
     * File key
     *
     * @var string
     */
    protected $_fileKey;

    public function execute()
    {
        try {
            $backgroundImage = array();
            $backgrounds = isset($_FILES['backgrounds']) ? $_FILES['backgrounds'] : array();

            foreach ($backgrounds as $key => $background){
                foreach ($background as $bg){
                    if($key == 'name'){
                        $backgroundImage['name'] = $bg[$this->_fileKey];
                    }
                    if($key == 'type'){
                        $backgroundImage['type'] = $bg[$this->_fileKey];
                    }
                    if($key == 'tmp_name'){
                        $backgroundImage['tmp_name'] = $bg[$this->_fileKey];
                    }
                    if($key == 'error'){
                        $backgroundImage['error'] = $bg[$this->_fileKey];
                    }
                    if($key == 'size'){
                        $backgroundImage['size'] = $bg[$this->_fileKey];
                    }
                }
            }
            if(!empty($backgroundImage)){
                $result = $this->imageUploader->saveFileToTmpDir($backgroundImage);
                $result['cookie'] = [
                    'name' => $this->_getSession()->getName(),
                    'value' => $this->_getSession()->getSessionId(),
                    'lifetime' => $this->_getSession()->getCookieLifetime(),
                    'path' => $this->_getSession()->getCookiePath(),
                    'domain' => $this->_getSession()->getCookieDomain(),
                ];
            }
            //$result = $this->imageUploader->saveFileToTmpDir($this->_fileKey);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
