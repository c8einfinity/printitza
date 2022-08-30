<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Color\View;

use Magento\Store\Model\ScopeInterface;

/**
 * Color color next and prev color links
 */
class NextPrev extends \Magento\Framework\View\Element\Template
{
    /**
     * Previous color
     *
     * @var \Designnbuy\Color\Model\Color
     */
    protected $_prevColor;

    /**
     * Next color
     *
     * @var \Designnbuy\Color\Model\Color
     */
    protected $_nextColor;

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Color\CollectionFactory
     */
    protected $_colorCollectionFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Designnbuy\Color\Model\ResourceModel\Color\CollectionFactory $_tagCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Color\Model\ResourceModel\Color\CollectionFactory $colorCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_colorCollectionFactory = $colorCollectionFactory;
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
            'dnbcolor/color_view/nextprev/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve prev color
     * @return \Designnbuy\Color\Model\Color || bool
     */
    public function getPrevColor()
    {
        if ($this->_prevColor === null) {
            $this->_prevColor = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'gteq' => $this->getColor()->getPublishTime()
                ])
                ->setOrder('publish_time', 'ASC')
                ->setPageSize(1);

            $color = $collection->getFirstItem();

            if ($color->getId()) {
                $this->_prevColor = $color;
            }
        }

        return $this->_prevColor;
    }

    /**
     * Retrieve next color
     * @return \Designnbuy\Color\Model\Color || bool
     */
    public function getNextColor()
    {
        if ($this->_nextColor === null) {
            $this->_nextColor = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'lteq' => $this->getColor()->getPublishTime()
                ])
                ->setOrder('publish_time', 'DESC')
                ->setPageSize(1);

            $color = $collection->getFirstItem();

            if ($color->getId()) {
                $this->_nextColor = $color;
            }
        }

        return $this->_nextColor;
    }

    /**
     * Retrieve color collection with frontend filters and order
     * @return bool
     */
    protected function _getFrontendCollection()
    {
        $collection = $this->_colorCollectionFactory->create();
        $collection->addActiveFilter()
            ->addFieldToFilter('color_id', ['neq' => $this->getColor()->getId()])
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'DESC')
            ->setPageSize(1);
        return $collection;
    }

    /**
     * Retrieve color instance
     *
     * @return \Designnbuy\Color\Model\Color
     */
    public function getColor()
    {
        return $this->_coreRegistry->registry('current_color_color');
    }

}
