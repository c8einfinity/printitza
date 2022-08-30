<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Color observer
 */
class SaveProductColorData implements ObserverInterface
{
    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Color\Product
     */
    protected $product;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Designnbuy\Color\Model\ResourceModel\Color\Product $product
    ) {
        $this->_request = $request;
        $this->product = $product;
    }
    /**
     * Predispath admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $this->_request;
        $links = $this->_request->getParam('links');
        if (is_array($links)) {
            foreach (['color'] as $linkType) {
                if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                    $linksData = $links[$linkType];
                    $_product = $observer->getProduct();
                    $this->product->saveProductRelation($_product, $linksData);
                }
            }
        }

    }
}
