<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Plugin\Catalog\Controller\Adminhtml\Product\Initialization;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper;
use Magento\Framework\App\RequestInterface;
use Designnbuy\OrderTicket\Model\Product\Source;

/**
 * Class HelperPlugin
 */
class HelperPlugin
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Setting default values according to config settings
     *
     * @param Helper $subject
     * @param ProductInterface $product
     * @return ProductInterface
     * @SuppressWarnings(PHPMD.UnusedFoorderticketlParameter)
     */
    public function afterInitialize(Helper $subject, ProductInterface $product)
    {
        if (!empty($this->request->getParam('product')['use_config_is_returnable'])) {
            $product->setData('is_returnable', Source::ATTRIBUTE_ENABLE_ORDERTICKET_USE_CONFIG);
        }

        return $product;
    }
}
