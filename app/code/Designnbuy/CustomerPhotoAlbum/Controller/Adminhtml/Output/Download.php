<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\CustomerPhotoAlbum\Controller\Adminhtml\Output;
use Magento\Framework\Controller\ResultFactory;

/**
 * Background home page view
 */
class Download extends \Magento\Backend\App\Action
{
    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    private $albumHelper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Inkscape $inkscapeHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Designnbuy\CustomerPhotoAlbum\Helper\Data $albumHelper
    ) {
        parent::__construct($context);
        $this->albumHelper = $albumHelper;
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
            $photo_ids = base64_decode($params['photo_id']);
            $folder_location = base64_decode($params['fl']);
            $file = $params['file'];

            $this->albumHelper->zipPhotoAlbum($photo_ids,$folder_location,$file);

        } catch (\Exception $e) {
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
