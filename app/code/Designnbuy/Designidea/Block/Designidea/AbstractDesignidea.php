<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Block\Designidea;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract designidea мшуц block
 */
abstract class AbstractDesignidea extends \Magento\Framework\View\Element\Template
{

    /**
     * Deprecated property. Do not use it.
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Designnbuy\Designidea\Model\Designidea
     */
    protected $_designidea;

    /**
     * Page factory
     *
     * @var \Designnbuy\Designidea\Model\DesignideaFactory
     */
    protected $_designideaFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var string
     */
    protected $_defaultDesignideaInfoBlock = 'Designnbuy\Designidea\Block\Designidea\Info';

    /**
     * @var \Designnbuy\Designidea\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Cms\Model\Page $designidea
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Cms\Model\PageFactory $designideaFactory
     * @param \Designnbuy\Designidea\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Designidea\Model\Designidea $designidea,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory,
        \Designnbuy\Designidea\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_designidea = $designidea;
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_designideaFactory = $designideaFactory;
        $this->_url = $url;
    }

    /**
     * Retrieve designidea instance
     *
     * @return \Designnbuy\Designidea\Model\Designidea
     */
    public function getDesignidea()
    {
        if (!$this->hasData('designidea')) {
            $this->setData('designidea',
                $this->_coreRegistry->registry('current_designidea_designidea')
            );
        }
        return $this->getData('designidea');
    }

    /**
     * Retrieve designidea short content
     *
     * @return string
     */
    public function getShorContent()
    {
        return $this->getDesignidea()->getShortFilteredContent();
    }

    /**
     * Retrieve designidea content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->getDesignidea()->getFilteredContent();
    }

    /**
     * Retrieve designidea info html
     *
     * @return string
     */
    public function getInfoHtml()
    {
        return $this->getInfoBlock()->toHtml();
    }

    /**
     * Retrieve designidea info block
     *
     * @return \Designnbuy\Designidea\Block\Designidea\Info
     */
    public function getInfoBlock()
    {
        $k = 'info_block';
        if (!$this->hasData($k)) {
            $blockName = $this->getDesignideaInfoBlockName();
            if ($blockName) {
                $block = $this->getLayout()->getBlock($blockName);
            }

            if (empty($block)) {
                $block = $this->getLayout()->createBlock($this->_defaultDesignideaInfoBlock, uniqid(microtime()));
            }

            $this->setData($k, $block);
        }

        return $this->getData($k)->setDesignidea($this->getDesignidea());
    }

}
