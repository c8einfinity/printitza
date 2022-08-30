<?php
namespace Designnbuy\Workflow\Ui\Component\Sales\Order;

class MassAction extends \Magento\Ui\Component\MassAction
{
    /**
     * @var \Designnbuy\Workflow\Helper\Data
     */
    protected $workflowData;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Designnbuy\Workflow\Helper\Data $workflowData,
        $components,
        array $data
    ) {
        $this->workflowData = $workflowData;
        parent::__construct($context, $components, $data);
    }

    public function prepare() {
        parent::prepare();
        if($this->workflowData->getWorkflowUser()) {
            $config = $this->getConfiguration();
            $allowedActions = [];
            /*foreach ($config['actions'] as $action) {
                if ('pdfinvoices_order' != $action['type']) {
                    $allowedActions[] = $action;
                }
            }*/
            $config = $allowedActions;
            //}
            $this->setData('config', (array)$config);
        }
    }
}