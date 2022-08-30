<?php

namespace Eadesigndev\PdfGeneratorPro\Model\Email;

use Eadesigndev\PdfGeneratorPro\Helper\Data as OpicmsData;
use Eadesigndev\PdfGeneratorPro\Model\Source\TemplateType;
use Magento\Sales\Model\Order\Email\Container\CreditmemoIdentity;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\InvoiceIdentity;
use Magento\Sales\Model\Order\Email\Container\OrderIdentity;
use Magento\Sales\Model\Order\Email\Container\ShipmentIdentity;
use Magento\Sales\Model\Order\Email\Container\Template;
use Eadesigndev\PdfGeneratorPro\Helper\Variable\Processors\Output;
use Eadesigndev\PdfGeneratorPro\Helper\Data;
use Magento\Framework\Stdlib\DateTime\DateTime;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    private $pdfTemplate;

    /**
     * @var Pdf
     */
    private $helper;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * SenderBuilder constructor.
     * @param Template $templateContainer
     * @param IdentityInterface $identityContainer
     * @param TransportBuilder $transportBuilder
     * @param Output $helper
     * @param Data $dataHelper
     * @param DateTime $dateTime
     */
    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        TransportBuilder $transportBuilder,
        Output $helper,
        Data $dataHelper,
        DateTime $dateTime
    ) {
        $this->helper = $helper;
        $this->dataHelper = $dataHelper;
        $this->dateTime = $dateTime;
        parent::__construct($templateContainer, $identityContainer, $transportBuilder);
    }

    /**
     * Add attachment to the main mail
     */
    public function send()
    {
        $this->addXteaPDFAttachment();
        parent::send();
    }

    /**
     * Add attachment to the css/bcc mail
     */
    public function sendCopyTo()
    {
        $this->addXteaPDFAttachment();
        parent::sendCopyTo();
    }

    /**
     * Add the attachment
     *
     * @return $this
     */
    private function addXteaPDFAttachment()
    {
        $templateSpecs = $this->entityByType();

        if (!array_key_exists('email', $templateSpecs)) {
            return $this;
        }

        $templateEmail = $templateSpecs['email'];

        if ($this->isForEmail($templateEmail)) {
            $templateValue = $templateSpecs['type'];
            $templateType = TemplateType::TYPES[$templateValue];
            $variables = $this->templateContainer->getTemplateVars();

            $source = $variables[$templateType];
            $this->helper->setSource($source);
            $this->pdfTemplate = $this->dataHelper->getTemplateStatus(
                $source,
                $templateValue
            );
            $this->attachment();
        }

        return $this;
    }

    /**
     * @param null $templateType
     *
     * @return bool
     */
    private function isForEmail($templateType = null)
    {
        if ($this->dataHelper->isAttachToEmailEnabled($templateType)) {
            return true;
        }

        return false;
    }

    /**
     * Create the actual pdf file attachment
     *
     * @return $this
     */
    private function attachment()
    {
        if (!$this->pdfTemplate) {
            return $this;
        }

        $helper = $this->helper;
        $helper->setTemplate($this->pdfTemplate);

        $pdfFileData = $helper->template2Pdf();
        $output = $helper->PDFmerger();

        $this->transportBuilder->addAttachment(
            $output,
            \Zend_Mime::TYPE_OCTETSTREAM,
            \Zend_Mime::DISPOSITION_ATTACHMENT,
            \Zend_Mime::ENCODING_BASE64,
            $pdfFileData['filename'] . '.pdf'
        );

        return $this;
    }

    /**
     * @return array
     */
    private function entityByType()
    {
        $identityContainer = $this->identityContainer;

        $result = [];
        if ($identityContainer instanceof OrderIdentity) {
            $result = [
                'email' => OpicmsData::EMAIL_ORDER,
                'type' => TemplateType::TYPE_ORDER];
        }

        if ($identityContainer instanceof InvoiceIdentity) {
            $result = [
                'email' => OpicmsData::EMAIL_INVOICE,
                'type' => TemplateType::TYPE_INVOICE];
        }

        if ($identityContainer instanceof ShipmentIdentity) {
            $result = [
                'email' => OpicmsData::EMAIL_SHIPMENT,
                'type' => TemplateType::TYPE_SHIPMENT];
        }

        if ($identityContainer instanceof CreditmemoIdentity) {
            $result = [
                'email' => OpicmsData::EMAIL_CREDITMEMO,
                'type' => TemplateType::TYPE_CREDIT_MEMO];
        }

        return $result;
    }
}
