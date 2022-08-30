<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Background observer
 */
class SaveProductFontData implements ObserverInterface
{
    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Background\Product
     */
    protected $product;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Designnbuy\Font\Model\ResourceModel\Font\Product $product
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
        $_product = $observer->getProduct();
        $request = $this->_request;
        $links = $this->_request->getParam('links');
        $links = isset($links) ? $links : ['font' => []];
        
        //$links = isset($links['links']) ? $links['links'] : ['printingmethod' => []];
        if (is_array($links)) {
            foreach (['font'] as $linkType) {
                if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                    $linksData = [];
                    foreach ($links[$linkType] as $item) {
                        $linksData[$item['id']] = [
                            'position' => isset($item['position']) ? $item['position'] : 0
                        ];
                    }
                    $links[$linkType] = $linksData;
                } else {
                    $links[$linkType] = [];
                }
                /*if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                    $linksData = $links[$linkType];
                    $_product = $observer->getProduct();
                    $this->product->saveProductRelation($_product, $linksData);
                }  else {
                    $links[$linkType] = [];
                }*/
            }
            $this->product->saveProductRelation($_product, $links);
        }

    }
}
