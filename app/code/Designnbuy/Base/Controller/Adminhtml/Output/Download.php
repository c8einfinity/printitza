<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Base\Controller\Adminhtml\Output;
use Magento\Framework\Controller\ResultFactory;

/**
 * Background home page view
 */
class Download extends \Magento\Backend\App\Action
{
    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    private $dnbOutputHelper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Inkscape $inkscapeHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Designnbuy\Base\Helper\Output $dnbOutputHelper,
        \Designnbuy\Base\Observer\Output $outputObserver
    ) {
        parent::__construct($context);
        $this->dnbOutputHelper = $dnbOutputHelper;
        $this->outputObserver = $outputObserver;
    }

    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
    /**
     * View background homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {



        try {
            $params = $this->getRequest()->getParams();

            $orderId = $params['id'];
            $info['title'] = $params['file'];
            $info['path'] = base64_decode($params['fl']).$info['title'];
            $outputfilepath = $this->dnbOutputHelper->getMediaPath().$info['path'];
            if(file_exists($outputfilepath)){
                unlink($outputfilepath);
            }
            
            if(!file_exists($outputfilepath) || isset($params['generate_output'])) {
                 $this->outputObserver->generateItemOutput($params['order_id'],$params['item_id'], 'automatic'); 
            }
            $this->dnbOutputHelper->downloadFile($info);

        } catch (\Exception $e) {
            echo "<pre>"; print_r($e->getMessage()); exit;
            $this->messageManager->addException(
                $e,
                __('Something went wrong while downloading output or output files are missing !!!')
            );
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('sales/order/view', ['order_id' => $orderId]);
            return $resultRedirect;
        }
    }

}
