<?php

namespace Designnbuy\Productattach\Plugin;

/**
 * Class ProductGet
 * @package Designnbuy\Productattach\Plugin
 */
class ProductGet
{
    /**
     * @var \Magento\Catalog\Api\Data\ProductExtensionFactory
     */
    protected $productExtensionFactory;

    /**
     * @var \Designnbuy\Productattach\Block\Attachment
     */
    protected $productAttachment;

    /**
     * ProductGet constructor.
     * @param \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory
     * @param \Designnbuy\Productattach\Block\Attachment $productAttachment
     */
    public function __construct(
        \Magento\Catalog\Api\Data\ProductExtensionFactory $productExtensionFactory,
        \Designnbuy\Productattach\Block\Attachment $productAttachment
    ) {
        $this->productExtensionFactory = $productExtensionFactory;
        $this->productAttachment = $productAttachment;
    }

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @return \Magento\Catalog\Api\Data\ProductInterface
     */
    public function afterGet(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductInterface $product
    ) {
        if($product->getExtensionAttributes() && $product->getExtensionAttributes()->getAttachments()) {
            return $product;
        }

        if(!$product->getExtensionAttributes()) {
            $productExtension = $this->productExtensionFactory->create();
            $product->setExtensionAttributes($productExtension);
        }

        $attachmentIds = [];
        $attachments = $this->productAttachment->getAttachment($product->getId());
        foreach ($attachments as $attachment) {
            $attachmentIds[] = $attachment->getId();
        }

        $product->getExtensionAttributes()->setAttachments($attachmentIds);

        return $product;
    }
}
