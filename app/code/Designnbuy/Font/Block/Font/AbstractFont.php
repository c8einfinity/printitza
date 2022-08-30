<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Block\Font;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract font мшуц block
 */
abstract class AbstractFont extends \Magento\Framework\View\Element\Template
{

    /**
     * Deprecated property. Do not use it.
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Designnbuy\Font\Model\Font
     */
    protected $_font;

    /**
     * Page factory
     *
     * @var \Designnbuy\Font\Model\FontFactory
     */
    protected $_fontFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $_defaultFontInfoBlock = 'Designnbuy\Font\Block\Font\Info';

    /**
     * @var \Designnbuy\Font\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $font
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\PageFactory $fontFactory
     * @param \Designnbuy\Font\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Font\Model\Font $font,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Font\Model\FontFactory $fontFactory,
        \Designnbuy\Font\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_font = $font;
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_fontFactory = $fontFactory;
        $this->_url = $url;
    }

    /**
     * Retrieve font instance
     *
     * @return \Designnbuy\Font\Model\Font
     */
    public function getFont()
    {
        if (!$this->hasData('font')) {
            $this->setData('font',
                $this->_coreRegistry->registry('current_font_font')
            );
        }
        return $this->getData('font');
    }

    /**
     * Retrieve font short content
     *
     * @return string
     */
    public function getShorContent()
    {
        return $this->getFont()->getShortFilteredContent();
    }

    /**
     * Retrieve font content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getFont()->getFilteredContent();
    }

    /**
     * Retrieve font info html
     *
     * @return string
     */
    public function getInfoHtml()
    {
        return $this->getInfoBlock()->toHtml();
    }

    /**
     * Retrieve font info block
     *
     * @return \Designnbuy\Font\Block\Font\Info
     */
    public function getInfoBlock()
    {
        $k = 'info_block';
        if (!$this->hasData($k)) {
            $blockName = $this->getFontInfoBlockName();
            if ($blockName) {
                $block = $this->getLayout()->getBlock($blockName);
            }

            if (empty($block)) {
                $block = $this->getLayout()->createBlock($this->_defaultFontInfoBlock, uniqid(microtime()));
            }

            $this->setData($k, $block);
        }

        return $this->getData($k)->setFont($this->getFont());
    }

}
