<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Returns;

class History extends \Magento\Framework\View\Element\Template
{
    /**
     * OrderTicket grid collection
     *
     * @var \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Initialize orderticket history content
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('return/history.phtml');
        /** @var $returns \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\Collection */
        $returns = $this->_collectionFactory->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'customer_id',
            $this->_customerSession->getCustomer()->getId()
        )->setOrder(
            'date_requested',
            'desc'
        );
        $this->setReturns($returns);
    }

    /**
     * Prepare orderticket returns history layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'sales.order.history.pager'
        )->setCollection(
            $this->getReturns()
        );
        $this->setChild('pager', $pager);
        $this->getReturns()->load();
        return $this;
    }

    /**
     * Get orderticket pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get orderticket view url
     *
     * @param \Magento\Framework\DataObject $return
     * @return string
     */
    public function getViewUrl($return)
    {
        return $this->getUrl('*/*/view', ['entity_id' => $return->getId()]);
    }

    /**
     * Get customer account back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
