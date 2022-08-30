<?php

namespace WeltPixel\FrontendOptions\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param int $storeId
     * @return mixed
     */
    public function getMobileTreshold($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/breakpoints/screen__m', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getPageMainWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/page_main', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }



    /**
     * @param int $storeId
     * @return mixed
     */
    public function getPageMainPadding($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/page_main_padding', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getFooterWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/footer', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getRowWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/row', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getDefaultPageWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/default_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getCmsPageWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/cms_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getCategoryPageWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/category_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getProductPageWidth($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/section_width/product_page', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBreakpointXXS($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/breakpoints/screen__xxs', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBreakpointXS($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/breakpoints/screen__xs', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBreakpointS($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/breakpoints/screen__s', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBreakpointM($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/breakpoints/screen__m', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBreakpointL($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/breakpoints/screen__l', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getBreakpointXL($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/breakpoints/screen__xl', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getAllBreakpoint($storeId = null) {
        return array(
            'xxs' => $this->getBreakpointXXS($storeId),
            'xs' => $this->getBreakpointXS($storeId),
            's' => $this->getBreakpointS($storeId),
            'm' => $this->getBreakpointM($storeId),
            'l' => $this->getBreakpointL($storeId),
            'xl' => $this->getBreakpointXL($storeId),
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getBreakPointsJson($storeId = null) {
        $brekpoints = [];
        $brekpoints['breakpoints'] = [];

        $minValue = 0;

        foreach ($this->getAllBreakpoint($storeId) as $key => $value) {
            $value = rtrim($value, 'px');
            $min = $minValue;
            $max = $value - 1;

            if ($key == 'xl') {
                $max = 10000;
            }

            $brekpoints['breakpoints'][$key] = [
                'enter' => $min,
                'exit'  => $max
            ];

            $minValue = $value;
        }

        return json_encode($brekpoints);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getContactPageVersion($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/contact_options/contact_version', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getTopImage($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/contact_options/top_image', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }


    /**
     * @param null $storeId
     * @return mixed
     */
    public function isEnabledBlock($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/contact_options/enable_block', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    /**
     * @return string
     */
    public function getContactBlockId($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/contact_options/block_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param null $storeId
     * @return mixed
     */
    public function getDefaultFontSettings($storeId = null)
    {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param int $storeId
     * @return mixed
     */
    public function getPrimaryMainColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/main_options/primary__color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    /**
     * @param int $storeId
     * @return mixed
     */
    public function getProductpageActionButtonVersionTwo($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/default_buttons/productpage_action_button_version_two', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    /**
     * @param int $storeId
     * @return mixed
     */
    public function getProductpageActionButtonHoverVersionTwo($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/default_buttons/productpage_action_button_hover_version_two', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    /**
     * @param int $storeId
     * @return mixed
     */
    public function getProductpageActionButtonTextVersionTwo($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/default_buttons/productpage_action_button_text_version_two', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    
    /**
     * @param int $storeId
     * @return mixed
     */
    public function getProductpageActionButtonTextHoverVersionTwo($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/default_buttons/productpage_action_button_text_hover_version_two', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    /**
     * @param int $storeId
     * @return mixed
     */
    public function getSecondaryMainColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/main_options/secondary__color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
    /**
     * @param int $storeId
     * @return mixed
     */
    public function getDefaultTextColor($storeId = null) {
        return $this->scopeConfig->getValue('weltpixel_frontend_options/default/text__color', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}
