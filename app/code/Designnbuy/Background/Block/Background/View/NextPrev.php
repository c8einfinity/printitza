<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Block\Background\View;

use Magento\Store\Model\ScopeInterface;

/**
 * Background background next and prev background links
 */
class NextPrev extends \Magento\Framework\View\Element\Template
{
    /**
     * Previous background
     *
     * @var \Designnbuy\Background\Model\Background
     */
    protected $_prevBackground;

    /**
     * Next background
     *
     * @var \Designnbuy\Background\Model\Background
     */
    protected $_nextBackground;

    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Background\CollectionFactory
     */
    protected $_backgroundCollectionFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Designnbuy\Background\Model\ResourceModel\Background\CollectionFactory $_tagCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Background\Model\ResourceModel\Background\CollectionFactory $backgroundCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_backgroundCollectionFactory = $backgroundCollectionFactory;
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
            'dnbbackground/background_view/nextprev/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve prev background
     * @return \Designnbuy\Background\Model\Background || bool
     */
    public function getPrevBackground()
    {
        if ($this->_prevBackground === null) {
            $this->_prevBackground = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'gteq' => $this->getBackground()->getPublishTime()
                ])
                ->setOrder('publish_time', 'ASC')
                ->setPageSize(1);

            $background = $collection->getFirstItem();

            if ($background->getId()) {
                $this->_prevBackground = $background;
            }
        }

        return $this->_prevBackground;
    }

    /**
     * Retrieve next background
     * @return \Designnbuy\Background\Model\Background || bool
     */
    public function getNextBackground()
    {
        if ($this->_nextBackground === null) {
            $this->_nextBackground = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'lteq' => $this->getBackground()->getPublishTime()
                ])
                ->setOrder('publish_time', 'DESC')
                ->setPageSize(1);

            $background = $collection->getFirstItem();

            if ($background->getId()) {
                $this->_nextBackground = $background;
            }
        }

        return $this->_nextBackground;
    }

    /**
     * Retrieve background collection with frontend filters and order
     * @return bool
     */
    protected function _getFrontendCollection()
    {
        $collection = $this->_backgroundCollectionFactory->create();
        $collection->addActiveFilter()
            ->addFieldToFilter('background_id', ['neq' => $this->getBackground()->getId()])
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'DESC')
            ->setPageSize(1);
        return $collection;
    }

    /**
     * Retrieve background instance
     *
     * @return \Designnbuy\Background\Model\Background
     */
    public function getBackground()
    {
        return $this->_coreRegistry->registry('current_background_background');
    }

}
