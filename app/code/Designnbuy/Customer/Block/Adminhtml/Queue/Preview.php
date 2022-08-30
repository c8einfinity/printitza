<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer template preview block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Customer\Block\Adminhtml\Queue;

class Preview extends \Designnbuy\Customer\Block\Adminhtml\Template\Preview
{
    /**
     * {@inheritdoc}
     */
    protected $profilerName = "designnbuy_customer_queue_proccessing";

    /**
     * @var \Designnbuy\Customer\Model\QueueFactory
     */
    protected $_queueFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Designnbuy\Customer\Model\TemplateFactory $templateFactory
     * @param \Designnbuy\Customer\Model\QueueFactory $queueFactory
     * @param \Designnbuy\Customer\Model\DesignFactory $designFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Designnbuy\Customer\Model\TemplateFactory $templateFactory,
        \Designnbuy\Customer\Model\DesignFactory $designFactory,
        \Designnbuy\Customer\Model\QueueFactory $queueFactory,
        array $data = []
    ) {
        $this->_queueFactory = $queueFactory;
        parent::__construct($context, $templateFactory, $designFactory, $data);
    }

    /**
     * @param \Designnbuy\Customer\Model\Template $template
     * @param string $id
     * @return $this
     */
    protected function loadTemplate(\Designnbuy\Customer\Model\Template $template, $id)
    {
        /** @var \Designnbuy\Customer\Model\Queue $queue */
        $queue = $this->_queueFactory->create()->load($id);
        $template->setTemplateType($queue->getCustomerType());
        $template->setTemplateText($queue->getCustomerText());
        $template->setTemplateStyles($queue->getCustomerStyles());
        return $this;
    }
}
