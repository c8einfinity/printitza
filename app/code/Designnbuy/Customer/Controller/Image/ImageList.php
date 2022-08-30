<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Customer\Controller\Image;


use Magento\Framework\Controller\ResultFactory;

/**
 * Background home page view
 */
class ImageList extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer Helper Class
     *
     * @var \Designnbuy\Customer\Helper\Data
     */
    protected $_customerHelper;


    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Output $outputHelper
     * @param \Designnbuy\Customer\Model\DesignFactory $designFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Designnbuy\Customer\Helper\Data $customerHelper
    ) {
        parent::__construct($context);
        $this->_customerHelper = $customerHelper;
    }
    /**
     * View background homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $result = $this->_customerHelper->getUserImages();

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }


        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

}
