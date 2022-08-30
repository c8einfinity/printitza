<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Base\Controller\Adminhtml\Vdp;
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
     * @var \Designnbuy\Base\Observer\Output
     */

    private $outputObserver;


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
        $params = $this->getRequest()->getParams();
        $zipName = $info['title'] = $params['file'];
        $outputPath = $info['path'] = base64_decode($params['fl']).$info['title'];

        if(file_exists($outputPath)){
            $this->dnbOutputHelper->downloadFile($info);
        } else {
            $orderId = $params['order_id'];
            $itemId = $params['item_id'];
            $this->outputObserver->generateItemOutput($orderId, $itemId, 'manual');
            $this->dnbOutputHelper->downloadFile($info);
        }
    }

}
