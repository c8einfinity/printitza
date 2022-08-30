<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Template;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract template мшуц block
 */
abstract class AbstractTemplate extends \Magento\Framework\View\Element\Template
{

    /**
     * Deprecated property. Do not use it.
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Designnbuy\Template\Model\Template
     */
    protected $_template;

    /**
     * Page factory
     *
     * @var \Designnbuy\Template\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $_defaultTemplateInfoBlock = 'Designnbuy\Template\Block\Template\Info';

    /**
     * @var \Designnbuy\Template\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $template
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\PageFactory $templateFactory
     * @param \Designnbuy\Template\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Template\Model\Template $template,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        \Designnbuy\Template\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_template = $template;
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_templateFactory = $templateFactory;
        $this->_url = $url;
    }

    /**
     * Retrieve template instance
     *
     * @return \Designnbuy\Template\Model\Template
     */
    public function getTemplate()
    {
        if (!$this->hasData('template')) {
            $this->setData('template',
                $this->_coreRegistry->registry('current_template_template')
            );
        }
        return $this->getData('template');
    }

    /**
     * Retrieve template short content
     *
     * @return string
     */
    public function getShorContent()
    {
        return $this->getTemplate()->getShortFilteredContent();
    }

    /**
     * Retrieve template content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getTemplate()->getFilteredContent();
    }

    /**
     * Retrieve template info html
     *
     * @return string
     */
    public function getInfoHtml()
    {
        return $this->getInfoBlock()->toHtml();
    }

    /**
     * Retrieve template info block
     *
     * @return \Designnbuy\Template\Block\Template\Info
     */
    public function getInfoBlock()
    {
        $k = 'info_block';
        if (!$this->hasData($k)) {
            $blockName = $this->getTemplateInfoBlockName();
            if ($blockName) {
                $block = $this->getLayout()->getBlock($blockName);
            }

            if (empty($block)) {
                $block = $this->getLayout()->createBlock($this->_defaultTemplateInfoBlock, uniqid(microtime()));
            }

            $this->setData($k, $block);
        }

        return $this->getData($k)->setTemplate($this->getTemplate());
    }

}
