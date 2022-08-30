<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Color;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract color мшуц block
 */
abstract class AbstractColor extends \Magento\Framework\View\Element\Template
{

    /**
     * Deprecated property. Do not use it.
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Designnbuy\Color\Model\Color
     */
    protected $_color;

    /**
     * Page factory
     *
     * @var \Designnbuy\Color\Model\ColorFactory
     */
    protected $_colorFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $_defaultColorInfoBlock = 'Designnbuy\Color\Block\Color\Info';

    /**
     * @var \Designnbuy\Color\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $color
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\PageFactory $colorFactory
     * @param \Designnbuy\Color\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Color\Model\Color $color,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Color\Model\ColorFactory $colorFactory,
        \Designnbuy\Color\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_color = $color;
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_colorFactory = $colorFactory;
        $this->_url = $url;
    }

    /**
     * Retrieve color instance
     *
     * @return \Designnbuy\Color\Model\Color
     */
    public function getColor()
    {
        if (!$this->hasData('color')) {
            $this->setData('color',
                $this->_coreRegistry->registry('current_color_color')
            );
        }
        return $this->getData('color');
    }

    /**
     * Retrieve color short content
     *
     * @return string
     */
    public function getShorContent()
    {
        return $this->getColor()->getShortFilteredContent();
    }

    /**
     * Retrieve color content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getColor()->getFilteredContent();
    }

    /**
     * Retrieve color info html
     *
     * @return string
     */
    public function getInfoHtml()
    {
        return $this->getInfoBlock()->toHtml();
    }

    /**
     * Retrieve color info block
     *
     * @return \Designnbuy\Color\Block\Color\Info
     */
    public function getInfoBlock()
    {
        $k = 'info_block';
        if (!$this->hasData($k)) {
            $blockName = $this->getColorInfoBlockName();
            if ($blockName) {
                $block = $this->getLayout()->getBlock($blockName);
            }

            if (empty($block)) {
                $block = $this->getLayout()->createBlock($this->_defaultColorInfoBlock, uniqid(microtime()));
            }

            $this->setData($k, $block);
        }

        return $this->getData($k)->setColor($this->getColor());
    }

}
