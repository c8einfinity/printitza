<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Block\Designidea\View;

use Magento\Store\Model\ScopeInterface;

/**
 * Designidea designidea next and prev designidea links
 */
class NextPrev extends \Magento\Framework\View\Element\Template
{
    /**
     * Previous designidea
     *
     * @var \Designnbuy\Designidea\Model\Designidea
     */
    protected $_prevDesignidea;

    /**
     * Next designidea
     *
     * @var \Designnbuy\Designidea\Model\Designidea
     */
    protected $_nextDesignidea;

    /**
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory
     */
    protected $_designideaCollectionFactory;

    /**
     * @var Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $_tagCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_designideaCollectionFactory = $designideaCollectionFactory;
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
            'dnbdesignidea/designidea_view/nextprev/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve prev designidea
     * @return \Designnbuy\Designidea\Model\Designidea || bool
     */
    public function getPrevDesignidea()
    {
        if ($this->_prevDesignidea === null) {
            $this->_prevDesignidea = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'gteq' => $this->getDesignidea()->getPublishTime()
                ])
                ->setOrder('publish_time', 'ASC')
                ->setPageSize(1);

            $designidea = $collection->getFirstItem();

            if ($designidea->getId()) {
                $this->_prevDesignidea = $designidea;
            }
        }

        return $this->_prevDesignidea;
    }

    /**
     * Retrieve next designidea
     * @return \Designnbuy\Designidea\Model\Designidea || bool
     */
    public function getNextDesignidea()
    {
        if ($this->_nextDesignidea === null) {
            $this->_nextDesignidea = false;
            $collection = $this->_getFrontendCollection()->addFieldToFilter(
                'publish_time', [
                    'lteq' => $this->getDesignidea()->getPublishTime()
                ])
                ->setOrder('publish_time', 'DESC')
                ->setPageSize(1);

            $designidea = $collection->getFirstItem();

            if ($designidea->getId()) {
                $this->_nextDesignidea = $designidea;
            }
        }

        return $this->_nextDesignidea;
    }

    /**
     * Retrieve designidea collection with frontend filters and order
     * @return bool
     */
    protected function _getFrontendCollection()
    {
        $collection = $this->_designideaCollectionFactory->create();
        $collection->addActiveFilter()
            ->addFieldToFilter('designidea_id', ['neq' => $this->getDesignidea()->getId()])
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'DESC')
            ->setPageSize(1);
        return $collection;
    }

    /**
     * Retrieve designidea instance
     *
     * @return \Designnbuy\Designidea\Model\Designidea
     */
    public function getDesignidea()
    {
        return $this->_coreRegistry->registry('current_designidea_designidea');
    }

}
