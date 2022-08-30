<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Block\Font\View;

use Magento\Store\Model\ScopeInterface;

/**
 * Font font next and prev font links
 */
class NextPrev extends \Magento\Framework\View\Element\Template
{
    /**
     * Previous font
     *
     * @var \Designnbuy\Font\Model\Font
     */
    protected $_prevFont;

    /**
     * Next font
     *
     * @var \Designnbuy\Font\Model\Font
     */
    protected $_nextFont;

    /**
     * @var \Designnbuy\Font\Model\ResourceModel\Font\CollectionFactory
     */
    protected $_fontCollectionFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Designnbuy\Font\Model\ResourceModel\Font\CollectionFactory $_tagCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Font\Model\ResourceModel\Font\CollectionFactory $fontCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_fontCollectionFactory = $fontCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Retrieve true if need to display next-prev links
     *
     * @return boolean
     */
    public function displayLinks()
    {
        return (bool)$this->_scopeConfig->getValue(
            'dnbfont/font_view/nextprev/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve prev font
     * @return \Designnbuy\Font\Model\Font || bool
     */
    public function getPrevFont()
    {
        if ($this->_prevFont === null) {
            $this->_prevFont = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'gteq' => $this->getFont()->getPublishTime()
                ])
                ->setOrder('publish_time', 'ASC')
                ->setPageSize(1);

            $font = $collection->getFirstItem();

            if ($font->getId()) {
                $this->_prevFont = $font;
            }
        }

        return $this->_prevFont;
    }

    /**
     * Retrieve next font
     * @return \Designnbuy\Font\Model\Font || bool
     */
    public function getNextFont()
    {
        if ($this->_nextFont === null) {
            $this->_nextFont = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'lteq' => $this->getFont()->getPublishTime()
                ])
                ->setOrder('publish_time', 'DESC')
                ->setPageSize(1);

            $font = $collection->getFirstItem();

            if ($font->getId()) {
                $this->_nextFont = $font;
            }
        }

        return $this->_nextFont;
    }

    /**
     * Retrieve font collection with frontend filters and order
     * @return bool
     */
    protected function _getFrontendCollection()
    {
        $collection = $this->_fontCollectionFactory->create();
        $collection->addActiveFilter()
            ->addFieldToFilter('font_id', ['neq' => $this->getFont()->getId()])
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'DESC')
            ->setPageSize(1);
        return $collection;
    }

    /**
     * Retrieve font instance
     *
     * @return \Designnbuy\Font\Model\Font
     */
    public function getFont()
    {
        return $this->_coreRegistry->registry('current_font_font');
    }

}
