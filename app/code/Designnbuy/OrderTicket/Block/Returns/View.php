<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Returns;

use Magento\Customer\Model\Context;
use Designnbuy\OrderTicket\Model\Item;
use Designnbuy\OrderTicket\Model\OrderTicket;

/**
 * Class View
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Values for each visible attribute
     *
     * @var array
     */
    protected $_realValueAttributes = [];

    /**
     * OrderTicket data
     *
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $_orderticketData = null;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerView = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * OrderTicket item collection
     *
     * @var \Designnbuy\OrderTicket\Model\ResourceModel\Item\CollectionFactory
     */
    protected $_itemsFactory;

    /**
     * OrderTicket status history collection
     *
     * @var \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\CollectionFactory
     */
    protected $_historiesFactory;

    /**
     * OrderTicket item factory
     *
     * @var \Designnbuy\OrderTicket\Model\ItemFactory
     */
    protected $_itemFactory;

    /**
     * Eav model form factory
     *
     * @var \Designnbuy\OrderTicket\Model\Item\FormFactory
     */
    protected $_itemFormFactory;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $customerData;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * Eav configuration model
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * Customer repository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var array
     */
    protected $attributes;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Data\Collection\ModelFactory $modelFactory
     * @param \Magento\Eav\Model\Form\Factory $formFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\CollectionFactory $historiesFactory
     * @param \Designnbuy\OrderTicket\Model\ItemFactory $itemFactory
     * @param Item\FormFactory $itemFormFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Magento\Customer\Helper\View $customerView
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketData
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Data\Collection\ModelFactory $modelFactory,
        \Magento\Eav\Model\Form\Factory $formFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\CollectionFactory $historiesFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Magento\Customer\Helper\View $customerView,
        \Magento\Framework\App\Http\Context $httpContext,
        \Designnbuy\OrderTicket\Helper\Data $orderticketData,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->_eavConfig = $eavConfig;
        $this->_historiesFactory = $historiesFactory;
        $this->currentCustomer = $currentCustomer;
        $this->_customerView = $customerView;
        $this->_orderticketData = $orderticketData;
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->customerRepository = $customerRepository;
        $this->_urlBuilder = $context->getUrlBuilder();
        parent::__construct($context, $data);
    }

    /**
     * Initialize orderticket return
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        if (!$this->_coreRegistry->registry('current_orderticket')) {
            return;
        }
        $this->setTemplate('return/view.phtml');

        $this->setOrderTicket($this->_coreRegistry->registry('current_orderticket'));
        $this->setOrder($this->_coreRegistry->registry('current_order'));


        /** @var $comments \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\Collection */
        $comments = $this->_historiesFactory->create()->addFilter('orderticket_entity_id', $this->getOrderTicket()->getEntityId());
        $this->setComments($comments);
    }



    /**
     * Gets values for each visible attribute
     *
     * Parameter $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param string[] $excludeAttr
     * @return array
     */
    protected function _getAdditionalData(array $excludeAttr = [])
    {
        $data = [];

        $items = $this->getItems();

        $itemForm = false;

        foreach ($items as $item) {
            if (!$itemForm) {
                /* @var $itemForm \Designnbuy\OrderTicket\Model\Item\Form */
                $itemForm = $this->_itemFormFactory->create();
                $itemForm->setFormCode('default')->setStore($this->getStore())->setEntity($item);
            }
            foreach ($itemForm->getAttributes() as $attribute) {
                $code = $attribute->getAttributeCode();
                if ($attribute->getIsVisible() && !in_array($code, $excludeAttr)) {
                    $value = $attribute->getFrontend()->getValue($item);
                    $data[$item->getId()][$code] = [
                        'label' => $attribute->getStoreLabel(),
                        'value' => $value,
                        'html' => '',
                    ];
                    if ($attribute->getFrontendInput() == 'image') {
                        $data[$item->getId()][$code]['html'] = $this->setEntity($item)->getAttributeHtml($attribute);
                    }
                    if (!isset($this->attributes[$code])) {
                        $this->attributes[$code] = $attribute->getStoreLabel();
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getVisibleAttributes()
    {
        $this->getRealValueAttributes();
        return $this->attributes;
    }

    /**
     * Gets attribute value by orderticket item id and attribute code
     *
     * @param  int $itemId
     * @param  string $attributeCode
     * @return string
     */
    public function getAttributeValue($itemId, $attributeCode)
    {
        if (empty($this->_realValueAttributes)) {
            $this->_realValueAttributes = $this->_getAdditionalData();
        }

        if (!isset($this->_realValueAttributes[$itemId][$attributeCode])) {
            return false;
        }

        if (!empty($this->_realValueAttributes[$itemId][$attributeCode]['html'])) {
            $html = $this->_realValueAttributes[$itemId][$attributeCode]['html'];
        } else {
            $html = $this->escapeHtml($this->_realValueAttributes[$itemId][$attributeCode]['value']);
        }
        return $html;
    }

    /**
     * Gets values for each visible attribute depending on item id
     *
     * @param null|int $itemId
     * @return array
     */
    public function getRealValueAttributes($itemId = null)
    {
        if (empty($this->_realValueAttributes)) {
            $this->_realValueAttributes = $this->_getAdditionalData();
        }
        if ($itemId && isset($this->_realValueAttributes[$itemId])) {
            return $this->_realValueAttributes[$itemId];
        } else {
            return $this->_realValueAttributes;
        }
    }

    /**
     * Gets attribute label by orderticket item id and attribute code
     *
     * @param  int $itemId
     * @param  string $attributeCode
     * @return string|false
     */
    public function getAttributeLabel($itemId, $attributeCode)
    {
        if (empty($this->_realValueAttributes)) {
            $this->_realValueAttributes = $this->_getAdditionalData();
        }

        if (isset($this->_realValueAttributes[$itemId][$attributeCode])) {
            return $this->_realValueAttributes[$itemId][$attributeCode]['label'];
        }

        return false;
    }

    /**
     * Gets item options
     *
     * @param  Item $item
     * @return array|bool
     */
    public function getItemOptions($item)
    {
        return $item->getOptions();
    }

    /**
     * Get sales order view url
     *
     * @param OrderTicket $orderticket
     * @return string
     */
    public function getOrderUrl($orderticket)
    {
        return $this->getUrl('sales/order/view/', ['order_id' => $orderticket->getOrderId()]);
    }

    /**
     * Get orderticket returns back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl('orderticket/returns/history');
        } else {
            return $this->getUrl('orderticket/guest/returns');
        }
    }

    /**
     * Get return address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->_orderticketData->getReturnAddress();
    }

    /**
     * Get add comment submit url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/addComment', ['entity_id' => (int)$this->getRequest()->getParam('entity_id')]);
    }

    /**
     * Get customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->_customerView->getCustomerName($this->getCustomerData());
        } else {
            $billingAddress = $this->_coreRegistry->registry('current_order')->getBillingAddress();

            $name = '';
            if ($this->_eavConfig->getAttribute('customer', 'prefix')->getIsVisible() && $billingAddress->getPrefix()
            ) {
                $name .= $billingAddress->getPrefix() . ' ';
            }
            $name .= $billingAddress->getFirstname();
            if ($this->_eavConfig->getAttribute(
                'customer',
                'middlename'
            )->getIsVisible() && $billingAddress->getMiddlename()
            ) {
                $name .= ' ' . $billingAddress->getMiddlename();
            }
            $name .= ' ' . $billingAddress->getLastname();
            if ($this->_eavConfig->getAttribute('customer', 'suffix')->getIsVisible() && $billingAddress->getSuffix()
            ) {
                $name .= ' ' . $billingAddress->getSuffix();
            }
            return $name;
        }
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerData()
    {
        if (empty($this->customerData)) {
            $customerId = $this->currentCustomer->getCustomerId();
            $this->customerData = $this->customerRepository->getById($customerId);
        }
        return $this->customerData;
    }

    /**
     * Get html data of tracking info block. Namely list of rows in table
     *
     * @return string
     */
    public function getTrackingInfo()
    {
        return $this->getBlockHtml('orderticket.returns.tracking');
    }

    /**
     * Get collection of tracking numbers of ORDERTICKET
     *
     * @return \Designnbuy\OrderTicket\Model\ResourceModel\Shipping\Collection
     */
    public function getTrackingNumbers()
    {
        return $this->getOrderTicket()->getTrackingNumbers();
    }

    /**
     * Get shipping label of ORDERTICKET
     *
     * @return \Designnbuy\OrderTicket\Model\Shipping
     */
    public function getShippingLabel()
    {
        return $this->getOrderTicket()->getShippingLabel();
    }

    /**
     * Get shipping label of ORDERTICKET
     *
     * @return \Designnbuy\OrderTicket\Model\Shipping
     */
    public function canShowButtons()
    {
        return (bool)($this->getShippingLabel()->getId() &&
            !($this->getOrderTicket()->getStatus() == \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_CLOSED ||
            $this->getOrderTicket()->getStatus() == \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_CLOSED));
    }

    /**
     * Get print label button html
     *
     * @return string
     */
    public function getPrintLabelButton()
    {
        $data['id'] = $this->getOrderTicket()->getId();
        $url = $this->getUrl('*/orderticket/printLabel', $data);
        return $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Link'
        )->setData(
            ['label' => __('Print Shipping Label'), 'onclick' => 'setLocation(\'' . $url . '\')']
        )->setAnchorText(
            __('Print Shipping Label')
        )->toHtml();
    }

    /**
     * Show packages button html
     *
     * @return string
     */
    public function getShowPackagesButton()
    {
        return $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Link'
        )->setData(
            [
                'href' => "javascript:void(0)",
                'title' => __('Show Packages'),
                'onclick' => "popWin(
                        '" .
                    $this->_orderticketData->getPackagePopupUrlByOrderTicketModel(
                        $this->getOrderTicket()
                    ) .
                "',
                        'package',
                        'width=800,height=600,top=0,left=0,resizable=yes,scrollbars=yes'); return false;",
            ]
        )->setAnchorText(
            __('Show Packages')
        )->toHtml();
    }

    /**
     * Show print shipping label html
     *
     * @return string
     */
    public function getPrintShippingLabelButton()
    {
        return $this->getLayout()->createBlock(
            'Magento\Framework\View\Element\Html\Link'
        )->setData(
            [
                'href' => $this->_orderticketData->getPackagePopupUrlByOrderTicketModel($this->getOrderTicket(), 'printlabel'),
                'title' => __('Print Shipping Label'),
            ]
        )->setAnchorText(
            __('Print Shipping Label')
        )->toHtml();
    }

    /**
     * Get list of shipping carriers for select
     *
     * @return array
     */
    public function getCarriers()
    {
        return $this->_orderticketData->getShippingCarriers($this->getOrderTicket()->getStoreId());
    }

    /**
     * Get url for add label action
     *
     * @return string
     */
    public function getAddLabelUrl()
    {
        return $this->getUrl('*/*/addLabel/', ['entity_id' => $this->getOrderTicket()->getEntityId()]);
    }

    /**
     * Get whether orderticket and allowed
     *
     * @return bool
     */
    public function isPrintShippingLabelAllowed()
    {
        return $this->getOrderTicket()->isAvailableForPrintLabel();
    }
    public function getFile($file)
    {
        $urlPath = \Designnbuy\OrderTicket\Helper\Data::FILE_PATH;
        return $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]).$urlPath.$file;
    }
}
