<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\CommissionReport\Controller\Adminhtml\Report;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportOrderCsv extends \Magento\Backend\App\Action
{
      protected $_fileFactory;
      protected $_response;
      protected $_view;
      protected $directory;
      protected $converter;
      protected $resultPageFactory ;
      protected $directory_list;
      public function __construct( \Magento\Backend\App\Action\Context  $context,
             \Magento\Framework\View\Result\PageFactory $resultPageFactory
            ) {
            $this->resultPageFactory  = $resultPageFactory;
            parent::__construct($context);
    }
     public function execute()
     {
        $fileName = 'order.csv';
        //$resultPage = $this->resultPageFactory ->create();
        //$content = $resultPage->getLayout()->getBlock('')->getCsv();;
        $content = $this->_view->getLayout()->createBlock('Designnbuy\CommissionReport\Block\Adminhtml\Order\Grid')->getCsvFile();
        $this->_sendUploadResponse($fileName, $content);

     }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream') {

         $this->_response->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true)
            ->setHeader('Content-Length', strlen($content), true)
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"', true)
            ->setHeader('Last-Modified', date('r'), true)
            ->setBody($content)
            ->sendResponse();
        die;

     }    
}
