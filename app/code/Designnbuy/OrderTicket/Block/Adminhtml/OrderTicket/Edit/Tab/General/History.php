<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General;

/**
 * Comments History Block at ORDERTICKET page
 */
class History extends \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General\AbstractGeneral
{
    /**
     * OrderTicket config model
     *
     * @var \Designnbuy\OrderTicket\Model\Config
     */
    protected $_orderticketConfig;

    /**
     * OrderTicket status history collection
     *
     * @var \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * OrderTicket source status factory
     *
     * @var \Designnbuy\OrderTicket\Model\OrderTicket\Source\StatusFactory
     */
    protected $_statusFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Designnbuy\OrderTicket\Model\Config $orderticketConfig
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Designnbuy\OrderTicket\Model\Config $orderticketConfig,
        \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\CollectionFactory $collectionFactory,
        \Designnbuy\OrderTicket\Model\OrderTicket\Source\StatusFactory $statusFactory,
        array $data = []
    ) {
        $this->_orderticketConfig = $orderticketConfig;
        $this->_collectionFactory = $collectionFactory;
        $this->_statusFactory = $statusFactory;
        $this->_urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $registry, $data);
    }

    /**
     * Prepare child blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $onclick = "submitAndReloadArea($('orderticket-history-block').parentNode, '" . $this->getSubmitUrl() . "')";
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData(
            ['label' => __('Submit Comment'), 'class' => 'action-save action-secondary', 'onclick' => $onclick]
        );
        $this->setChild('submit_button', $button);

        return parent::_prepareLayout();
    }

    /**
     * Get config value - is Enabled ORDERTICKET Comments Email
     *
     * @return bool
     */
    public function canSendCommentEmail()
    {
        $this->_orderticketConfig->init($this->_orderticketConfig->getRootCommentEmail(), $this->getOrder()->getStore());
        return $this->_orderticketConfig->isEnabled();
    }

    /**
     * Get config value - is Enabled ORDERTICKET Email
     *
     * @return bool
     */
    public function canSendConfiordertickettionEmail()
    {
        $this->_orderticketConfig->init($this->_orderticketConfig->getRootOrderTicketEmail(), $this->getOrder()->getStore());
        return $this->_orderticketConfig->isEnabled();
    }

    /**
     * Get URL to add comment action
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('adminhtml/*/addComment', ['id' => $this->getOrderTicketData('entity_id')]);
    }

    /**
     * Get comments
     *
     * @return \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\Collection
     */
    public function getComments()
    {
        /** @var $collection \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\Collection */
        $collection = $this->_collectionFactory->create();
        return $collection->addFieldToFilter('orderticket_entity_id', $this->_coreRegistry->registry('current_orderticket')->getId());
    }

    /**
     * Get stat uses
     *
     * @return array
     */
    public function getStatuses()
    {
        $sourceStatus = $this->_statusFactory->create();
        $statuses = $sourceStatus->getOptionArray();
        return $statuses;
    }

    public function getFile($file)
    {
        $urlPath = \Designnbuy\OrderTicket\Helper\Data::FILE_PATH;
        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).$urlPath.$file;
    }
}
