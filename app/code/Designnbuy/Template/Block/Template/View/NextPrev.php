<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Template\View;

use Magento\Store\Model\ScopeInterface;

/**
 * Template template next and prev template links
 */
class NextPrev extends \Magento\Framework\View\Element\Template
{
    /**
     * Previous template
     *
     * @var \Designnbuy\Template\Model\Template
     */
    protected $_prevTemplate;

    /**
     * Next template
     *
     * @var \Designnbuy\Template\Model\Template
     */
    protected $_nextTemplate;

    /**
     * @var \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templateCollectionFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $_tagCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_templateCollectionFactory = $templateCollectionFactory;
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
            'dnbtemplate/template_view/nextprev/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve prev template
     * @return \Designnbuy\Template\Model\Template || bool
     */
    public function getPrevTemplate()
    {
        if ($this->_prevTemplate === null) {
            $this->_prevTemplate = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'gteq' => $this->getTemplate()->getPublishTime()
                ])
                ->setOrder('publish_time', 'ASC')
                ->setPageSize(1);

            $template = $collection->getFirstItem();

            if ($template->getId()) {
                $this->_prevTemplate = $template;
            }
        }

        return $this->_prevTemplate;
    }

    /**
     * Retrieve next template
     * @return \Designnbuy\Template\Model\Template || bool
     */
    public function getNextTemplate()
    {
        if ($this->_nextTemplate === null) {
            $this->_nextTemplate = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'lteq' => $this->getTemplate()->getPublishTime()
                ])
                ->setOrder('publish_time', 'DESC')
                ->setPageSize(1);

            $template = $collection->getFirstItem();

            if ($template->getId()) {
                $this->_nextTemplate = $template;
            }
        }

        return $this->_nextTemplate;
    }

    /**
     * Retrieve template collection with frontend filters and order
     * @return bool
     */
    protected function _getFrontendCollection()
    {
        $collection = $this->_templateCollectionFactory->create();
        $collection->addActiveFilter()
            ->addFieldToFilter('template_id', ['neq' => $this->getTemplate()->getId()])
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'DESC')
            ->setPageSize(1);
        return $collection;
    }

    /**
     * Retrieve template instance
     *
     * @return \Designnbuy\Template\Model\Template
     */
    public function getTemplate()
    {
        return $this->_coreRegistry->registry('current_template_template');
    }

}
