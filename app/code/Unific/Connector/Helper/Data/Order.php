<?php

namespace Unific\Connector\Helper\Data;

use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;
use Unific\Connector\Helper\Filter;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\ExtensibleDataObjectConverter;

class Order
{
    /**
     * @var Filter
     */
    protected $filterHelper;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $dataObjectConverter;
    /**
     * @var OrderInterface|Order
     */
    protected $order;
    /**
     * @var Invoice
     */
    protected $invoice;
    /**
     * @var Shipment
     */
    protected $shipment;
    /**
     * @var array
     */
    protected $returnData = [];

    /**
     * OrderPlugin constructor.
     * @param Filter $filterHelper
     * @param Session $customerSession
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     */
    public function __construct(
        Filter $filterHelper,
        Session $customerSession,
        ExtensibleDataObjectConverter $dataObjectConverter
    ) {
        $this->filterHelper = $filterHelper;
        $this->customerSession = $customerSession;
        $this->dataObjectConverter = $dataObjectConverter;
    }

    /**
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
        $this->setOrderInfo();
    }

    /**
     * @param InvoiceInterface $invoice
     */
    public function setInvoice(InvoiceInterface $invoice)
    {
        $this->invoice = $invoice;
        $this->setOrder($invoice->getOrder());
    }

    /**
     * @param ShipmentInterface $shipment
     */
    public function setShipment(ShipmentInterface $shipment)
    {
        $this->shipment = $shipment;
        $this->setOrder($shipment->getOrder());
    }

    /**
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return void
     */
    protected function setOrderInfo()
    {
        $this->returnData = $this->filterHelper->processObjectData($this->order->getData());
        $this->returnData['order_items'] = array_values($this->returnData['items']);
        unset($this->returnData['items']);

        $this->returnData['addresses'] = [];
        if ($this->order->getBillingAddress() !== null) {
            $this->returnData['addresses']['billing'] = $this->dataObjectConverter->toFlatArray(
                $this->order->getBillingAddress(),
                [],
                OrderAddressInterface::class
            );
            $this->returnData['addresses']['billing'] = $this->filterHelper->fixAddressKey(
                $this->returnData['addresses']['billing'],
                'street'
            );
        }

        if ($this->order->getShippingAddress() !== null) {
            $this->returnData['addresses']['shipping'] = $this->dataObjectConverter->toFlatArray(
                $this->order->getShippingAddress(),
                [],
                OrderAddressInterface::class
            );
            $this->returnData['addresses']['shipping'] = $this->filterHelper->fixAddressKey(
                $this->returnData['addresses']['shipping'],
                'street'
            );
        }

        if (isset($this->returnData['customer_is_guest'])) {
            $this->returnData['customer_is_guest'] = (int)$this->returnData['customer_is_guest'];
        }

        // Sanitize order payment
        if ($this->order->getPayment()) {
            $this->returnData['payment'] = array_intersect_key(
                $this->order->getPayment()->getData(),
                array_flip($this->filterHelper->getPaymentWhitelist($this->order->getStoreId()))
            );
        }

        $this->returnData['notes'] = [];
        
        // Order Notes
        if ($this->order->getStatusHistoryCollection()) {
            foreach ($this->order->getStatusHistoryCollection() as $status) {
                if ($status->getComment()) {
                    $this->returnData['notes'][] = [
                        'comment' => $status->getComment()
                    ];
                }
            }
        }

        if ($this->invoice) {
            $this->returnData['updated_at'] = $this->invoice->getUpdatedAt();

            // Invoice Notes
            if ($this->invoice->getComments()) {
                foreach ($this->invoice->getComments() as $comments) {
                        $this->returnData['notes'][] = [
                        'comment' => $comments->getComment()
                    ];
                }
            }
        }

        if ($this->shipment) {
            $shippingCarrier = explode('_', $this->order->getShippingMethod());
            $this->returnData['shipment'] = [
                'shipping_carrier' => array_key_exists(0, $shippingCarrier) ? $shippingCarrier[0] : null,
                'shipping_method' => array_key_exists(1, $shippingCarrier) ? $shippingCarrier[1] : null,
                'shipping_name' => $this->order->getShippingDescription()
            ];

            if ($this->shipment->getTracks()) {
                $this->returnData['shipment']['tracking'] = [];
                $processedTrackings = [];
                foreach ($this->shipment->getTracks() as $tracking) {
                    if (in_array($tracking->getTrackNumber(), $processedTrackings)) {
                        // filtering is needed because with Shipment::addTrack() method may duplicate new tracking
                        continue;
                    }
                    $this->returnData['shipment']['tracking'][] = [
                        'number' => $tracking->getTrackNumber(),
                        'created_at' => $tracking->getCreatedAt() ?:
                            ($this->shipment->getCreatedAt() ?: date('Y-m-d H:i:s')),
                        'carrier_code' => $tracking->getCarrierCode(),
                        'carrier_title' => $tracking->getTitle()
                    ];
                    $processedTrackings[] = $tracking->getTrackNumber();
                }
            }

            // Shipment Notes
            if ($this->shipment->getComments()) {
                foreach ($this->shipment->getComments() as $comments) {
                    $this->returnData['notes'][] = [
                        'comment' => $comments->getComment()
                    ];
                }
            }

            $this->returnData['updated_at'] = $this->shipment->getUpdatedAt();
        } else {
            $this->returnData['shipments'] = [];
            if ($this->order->getShipmentsCollection() && $this->order->hasShipments()) {
                foreach ($this->order->getShipmentsCollection() as $shipment) {
                    $this->returnData['shipments'][] = $this->getShipmentData($shipment);
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getOrderInfo()
    {
        return $this->filterHelper->sanitizeAddressData($this->returnData);
    }

    /**
     * @param ShipmentInterface $shipment
     * @return array
     */
    protected function getShipmentData(ShipmentInterface $shipment)
    {
        $shipmentData = $this->dataObjectConverter->toFlatArray(
            $shipment,
            [],
            ShipmentInterface::class
        );
        if ($shipment->getTracks()) {
            $shipmentData['tracking'] = [];
            foreach ($shipment->getTracks() as $tracking) {
                $shipmentData['tracking'][] = [
                    'number' => $tracking->getTrackNumber(),
                    'created_at' => $tracking->getCreatedAt(),
                    'carrier_code' => $tracking->getCarrierCode(),
                    'carrier_title' => $tracking->getTitle()
                ];
            }
        }

        return $shipmentData;
    }
}
