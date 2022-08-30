<?php

namespace Designnbuy\JobManagement\Block\Adminhtml\Edit;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class CreateOrderTicketButton
 */
class CreateOrderTicketButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var \Designnbuy\Workflow\Helper\Data
     */
    protected $workflowData;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        UrlInterface $urlBuilder,
        \Designnbuy\JobManagement\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_urlBuilder = $urlBuilder;
        $this->_helper = $helper;
    }
    /**
     * @return array
     */
    public function getButtonData()
    {
        $entityId = $this->getEntityId();
        $job = $this->_helper->getJobContent($entityId);
        $jobTitle = $job->getTitle();
        if(isset($jobTitle) && $jobTitle != ""){
            $disabledClass = "";
        }else{
            $disabledClass = "disabled";
        }
        return [
            'label' => __('Create Ticket'),
            'class' => ''.$disabledClass.' create-ticket',
            'on_click' => 'setLocation(\'' . $this->createOrderTicket() . '\')',
            'sort_order' => 80,
        ];
    }

    public function createOrderTicket(){
        return  $this->_urlBuilder->getUrl(
            'adminhtml/orderticket/new',['order_id' => $this->getOrderId(), 'job_id' => $this->getEntityId()]
        );
    }

    public function getOrderId()
    {
        return $this->context->getRequest()->getParam('order_id');
    }

    public function getEntityId()
    {
        return $this->context->getRequest()->getParam('id');
    }
}
