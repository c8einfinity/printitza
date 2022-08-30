<?php

namespace Eadesigndev\PdfGeneratorPro\Helper;

use Magento\Catalog\Model\Product;
use Eadesigndev\PdfGeneratorPro\Model\Pdfgenerator;
use Eadesigndev\PdfGeneratorPro\Model\Source\TemplatePaperOrientation;
use Eadesigndev\PdfGeneratorPro\Model\Template\Processor;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Magento\Payment\Helper\Data as PaymentData;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Address\Renderer;
use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;

/**
 * Class AbstractPDF
 *
 * @package Eadesigndev\PdfGeneratorPro\Helper
 * @SuppressWarnings(CouplingBetweenObjects)
 */
abstract class AbstractPDF extends AbstractHelper
{

    /**
     * Paper orientation
     */
    const PAPER_ORI = [
        1 => 'P',
        2 => 'L'
    ];

    /**
     * Paper size
     */
    const PAPER_SIZE = [
        1 => 'A4-',
        2 => 'A3-',
        3 => 'A5-',
        4 => 'A6-',
        5 => 'LETTER-',
        6 => 'LEGAL-'
    ];

    /**
     * Object date fields
     */
    const DATE_FIELDS = [
        'created_at',
        'updated_at'
    ];

    /**
     * Object date fields
     */
    const JOB_DATE_FIELDS = [        
        'created_date',
        'start_date',
        'due_date'
    ];

    const CODE_BAR = [
        'ean13',
        'isbn',
        'issn',
        'upca',
        'upce',
        'ean8',
        'imb',
        'rm4scc',
        'kix',
        'postnet',
        'planet',
        'c128a',
        'c128b',
        'c128c',
        'ean128a',
        'ean128b',
        'ean128c',
        'c39',
        'c39+',
        'c39e',
        'c39e+',
        's25',
        's25+',
        'i25',
        'i25+',
        'i25b',
        'i25b+',
        'c93',
        'msi',
        'msi+',
        'codabar',
        'code11',
        'QR'
    ];

    /**
     * @var $context
     */
    public $context;

    /**
     * @var Processor
     */
    public $processor;

    /**
     * @var
     */
    public $order;

    /**
     * @var invoice;
     */
    public $source;

    /**
     * @var template
     */
    public $template;

    /**
     * @var the customer data fom the order
     */
    public $customer;
    /**
     * @var InvoiceIdentity
     */
    public $identityContainer;

    /**
     * @var PaymentData
     */
    public $paymentHelper;

    /**
     * @var Renderer
     */
    public $addressRenderer;

    /**
     * AbstractPDF constructor.
     * @param Context $context
     * @param Processor $processor
     * @param PaymentData $paymentHelper
     * @param InvoiceIdentity $identityContainer
     * @param Renderer $addressRenderer
     */
    public function __construct(
        Context $context,
        Processor $processor,
        PaymentData $paymentHelper,
        InvoiceIdentity $identityContainer,
        Renderer $addressRenderer
    ) {
        $this->processor = $processor;
        $this->paymentHelper = $paymentHelper;
        $this->identityContainer = $identityContainer;
        $this->addressRenderer = $addressRenderer;
        parent::__construct($context);
    }

    /**
     * @param $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;

        if ($source instanceof Order || $source instanceof Product) {
            $this->setOrder($source);
        } else {
            $this->setOrder($source->getOrder());
        }

        return $this;
    }

    /**
     * @param Order $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param Pdfgenerator $template
     * @return $this
     */
    public function setTemplate(Pdfgenerator $template)
    {
        $this->template = $template;
        $this->processor->setPDFTemplate($template);
        return $this;
    }

    /**
     * @param DataObject $customer
     * @return $this
     */
    public function setCustomer(DataObject $customer)
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @param Order $order
     * @return mixed
     */
    public function getPaymentHtml(Order $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $this->identityContainer->getStore()->getStoreId()
        );
    }

    /**
     * @param Order $order
     * @return null
     */
    public function getFormattedShippingAddress(Order $order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }

    /**
     * @param Order $order
     * @return null|string
     */
    public function getFormattedBillingAddress(Order $order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }

    /**
     * Get the format and orientation, ex: A4-L
     * @param $form
     * @param $ori
     * @return string
     */
    public function paperFormat($form, $ori)
    {
        $size = self::PAPER_SIZE;
        $oris = self::PAPER_ORI;

        if ($ori == TemplatePaperOrientation::TEMAPLATE_PAPER_PORTRAIT) {
            return str_replace('-', '', $size[$form]);
        }

        $format = $size[$form] . $oris[$ori];

        return $format;
    }
}
