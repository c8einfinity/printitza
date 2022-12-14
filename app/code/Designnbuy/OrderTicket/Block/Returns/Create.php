<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Returns;

use Designnbuy\OrderTicket\Model\Item\Attribute;
use Magento\Sales\Model\Order\Item;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Create extends \Designnbuy\OrderTicket\Block\Form
{
    /**
     * OrderTicket data
     *
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $_orderticketData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;


    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Collection\ModelFactory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Designnbuy\OrderTicket\Model\ItemFactory $itemFactory
     * @param \Designnbuy\OrderTicket\Model\Item\FormFactory $itemFormFactory
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketData
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Collection\ModelFactory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Designnbuy\OrderTicket\Helper\Data $orderticketData,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_orderticketData = $orderticketData;
        $this->addressRenderer = $addressRenderer;
        parent::__construct($context, $data);
    }

    /**
     * Initialize current order
     *
     * @return void
     */
    public function _construct()
    {
        $order = $this->_coreRegistry->registry('current_order');
        if (!$order) {
            return;
        }
        $this->setOrder($order);


        $formData = $this->_session->getOrderTicketFormData(true);
        if (!empty($formData)) {
            $data = new \Magento\Framework\DataObject();
            $data->addData($formData);
            $this->setFormData($data);
        }
        $errorKeys = $this->_session->getOrderTicketErrorKeys(true);
        if (!empty($errorKeys)) {
            $data = new \Magento\Framework\DataObject();
            $data->addData($errorKeys);
            $this->setErrorKeys($data);
        }
    }

    /**
     * Retrieves item qty available for return
     *
     * @param  Item $item
     * @return int
     */
    public function getAvailableQty($item)
    {
        $return = $item->getAvailableQty();
        if (!$item->getIsQtyDecimal()) {
            $return = intval($return);
        }
        return $return;
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->_urlBuilder->getUrl('sales/order/history');
    }

    /**
     * Prepare orderticket item attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        /* @var $itemModel \Designnbuy\OrderTicket\Model\Item */
        $itemModel = $this->_itemFactory->create();

        /* @var $itemForm \Designnbuy\OrderTicket\Model\Item\Form */
        $itemForm = $this->_itemFormFactory->create();
        $itemForm->setFormCode('default')->setStore($this->getStore())->setEntity($itemModel);

        // prepare item attributes to show
        $attributes = [];

        // add system required attributes
        foreach ($itemForm->getSystemAttributes() as $attribute) {
            /* @var $attribute Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        // add user defined attributes
        foreach ($itemForm->getUserAttributes() as $attribute) {
            /* @var $attribute Attribute */
            if ($attribute->getIsVisible()) {
                $attributes[$attribute->getAttributeCode()] = $attribute;
            }
        }

        uasort(
            $attributes,
            // @codingStandardsIgnoreStart
            /**
             * Compares sort order of attributes, returns -1, 0 or 1 if $a sort
             * order is less, equal or greater than $b sort order respectively.
             *
             * @param Attribute $a
             * @param Attribute $b
             *
             * @return int
             */
            // @codingStandardsIgnoreEnd
            function (Attribute $a, Attribute $b) {
                $diff = $a->getSortOrder() - $b->getSortOrder();
                return $diff ? ($diff > 0 ? 1 : -1) : 0;
            }
        );

        return $attributes;
    }

    /**
     * Retrieves Contact Email Address on error
     *
     * @return string
     */
    public function getContactEmail()
    {
        $data = $this->getFormData();
        $email = '';

        if ($data) {
            $email = $this->escapeHtml($data->getCustomerCustomEmail());
        }
        return $email;
    }

    /**
     * @param \Magento\Sales\Model\Order\Address $address
     * @param string $foordertickett
     * @return null|string
     */
    public function format(\Magento\Sales\Model\Order\Address $address, $foordertickett)
    {
        return $this->addressRenderer->format($address, $foordertickett);
    }
}
