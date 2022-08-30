<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Base\Controller\Adminhtml\Image;
use Magento\Framework\Controller\ResultFactory;
use Designnbuy\Base\Helper\Data as DnbBaseHelper;
/**
 * Background home page view
 */
class Download extends \Magento\Backend\App\Action
{
    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    private $dnbBaseHelper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Inkscape $inkscapeHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        DnbBaseHelper $dnbBaseHelper
    ) {
        parent::__construct($context);
        $this->dnbBaseHelper = $dnbBaseHelper;
    }
    /**
     * View background homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        try {
            $url = $this->getRequest()->getParam('url', false);
            $result = $this->dnbBaseHelper->download($url, $front = 0);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

}
