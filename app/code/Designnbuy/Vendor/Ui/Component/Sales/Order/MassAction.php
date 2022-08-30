<?php
namespace Designnbuy\Vendor\Ui\Component\Sales\Order;

class MassAction extends \Magento\Ui\Component\MassAction
{
    /**
     * @var \Designnbuy\Vendor\Helper\Data
     */
    protected $vendorData;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Designnbuy\Vendor\Helper\Data $vendorData,
        $components,
        array $data
    ) {
        $this->vendorData = $vendorData;
        parent::__construct($context, $components, $data);
    }

    public function prepare() {
        parent::prepare();
        if($this->vendorData->getVendorUser()) {
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