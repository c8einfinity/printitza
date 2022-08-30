<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Block\Background;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract background мшуц block
 */
abstract class AbstractBackground extends \Magento\Framework\View\Element\Template
{

    /**
     * Deprecated property. Do not use it.
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Designnbuy\Background\Model\Background
     */
    protected $_background;

    /**
     * Page factory
     *
     * @var \Designnbuy\Background\Model\BackgroundFactory
     */
    protected $_backgroundFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $_defaultBackgroundInfoBlock = 'Designnbuy\Background\Block\Background\Info';

    /**
     * @var \Designnbuy\Background\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $background
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\PageFactory $backgroundFactory
     * @param \Designnbuy\Background\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Background\Model\Background $background,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Background\Model\BackgroundFactory $backgroundFactory,
        \Designnbuy\Background\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_background = $background;
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_backgroundFactory = $backgroundFactory;
        $this->_url = $url;
    }

    /**
     * Retrieve background instance
     *
     * @return \Designnbuy\Background\Model\Background
     */
    public function getBackground()
    {
        if (!$this->hasData('background')) {
            $this->setData('background',
                $this->_coreRegistry->registry('current_background_background')
            );
        }
        return $this->getData('background');
    }

    /**
     * Retrieve background short content
     *
     * @return string
     */
    public function getShorContent()
    {
        return $this->getBackground()->getShortFilteredContent();
    }

    /**
     * Retrieve background content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getBackground()->getFilteredContent();
    }

    /**
     * Retrieve background info html
     *
     * @return string
     */
    public function getInfoHtml()
    {
        return $this->getInfoBlock()->toHtml();
    }

    /**
     * Retrieve background info block
     *
     * @return \Designnbuy\Background\Block\Background\Info
     */
    public function getInfoBlock()
    {
        $k = 'info_block';
        if (!$this->hasData($k)) {
            $blockName = $this->getBackgroundInfoBlockName();
            if ($blockName) {
                $block = $this->getLayout()->getBlock($blockName);
            }

            if (empty($block)) {
                $block = $this->getLayout()->createBlock($this->_defaultBackgroundInfoBlock, uniqid(microtime()));
            }

            $this->setData($k, $block);
        }

        return $this->getData($k)->setBackground($this->getBackground());
    }

}
