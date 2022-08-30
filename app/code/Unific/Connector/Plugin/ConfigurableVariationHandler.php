<?php
namespace Unific\Connector\Plugin;

use Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter;
use Magento\ConfigurableProduct\Model\Product\VariationHandler;

class ConfigurableVariationHandler
{
    /**
     * When generating variation for configurable product with images set in the grid we need to have media_type
     * field to be able to convert product data to flat array in the product save observer.
     * This plugin ensures it is set as Magento does not set the field itself.
     *
     * @param VariationHandler $variationHandler
     * @param array $productData
     * @return array
     */
    public function afterProcessMediaGallery(VariationHandler $variationHandler, array $productData)
    {
        if (array_key_exists('media_gallery', $productData)
            && array_key_exists('images', $productData['media_gallery'])
        ) {
            foreach ($productData['media_gallery']['images'] as &$image) {
                if (!array_key_exists('media_type', $image)) {
                    $image['media_type'] = ImageEntryConverter::MEDIA_TYPE_CODE;
                }
            }
        }

        return $productData;
    }
}
