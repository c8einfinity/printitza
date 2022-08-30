<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Controller\Adminhtml\Upload\Image;

use Magento\Framework\Controller\ResultFactory;

/**
 * Font image upload controller
 */
abstract class Action extends \Magento\Backend\App\Action
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

            if($this->_fileKey == 'woff'){
                $this->imageUploader->setAllowedExtensions(['woff']);
            } elseif ($this->_fileKey == 'js'){
                $this->imageUploader->setAllowedExtensions(['js']);
            }
            elseif (in_array($this->_fileKey,array('ttf','ttfbold','ttfitalic','ttfbolditalic'))){
                $this->imageUploader->setAllowedExtensions(['ttf']);
            }
            $result = $this->imageUploader->saveFileToTmpDir($this->_fileKey);

            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
