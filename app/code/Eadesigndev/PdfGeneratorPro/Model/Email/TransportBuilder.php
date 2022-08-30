<?php

namespace Eadesigndev\PdfGeneratorPro\Model\Email;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /**
     *
     * Add attachments to the email
     *
     * @param $body
     * @param $mimeType
     * @param $disposition
     * @param $encoding
     * @param null $filename
     * @return $this
     */
    public function addAttachment(
        $body,
        $mimeType = \Zend_Mime::TYPE_OCTETSTREAM,
        $disposition = \Zend_Mime::DISPOSITION_ATTACHMENT,
        $encoding = \Zend_Mime::ENCODING_BASE64,
        $filename = null
    ) {
        $this->message->createAttachment(
            $body,
            $mimeType,
            $disposition,
            $encoding,
            $filename
        );

        return $this;
    }
}
