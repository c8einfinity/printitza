<?php

namespace Eadesigndev\PdfGeneratorPro\Controller\Adminhtml\Variable;

use Eadesigndev\PdfGeneratorPro\Model\Source\TemplateType;
use Magento\Framework\Controller\Result\Json;

class Barcodes extends Template
{
    /**
     * @return $this|null
     */
    public function execute()
    {

        $this->_initTemplate();

        $id = $this->getRequest()->getParam('template_id');

        if (!$id) {
            return null;
        }

        $templateModel = $this->templateRepository->getById($id);
        $templateType = $templateModel->getData('template_type');

        $templateTypeName = TemplateType::TYPES[$templateType];

        /** @var Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $collection = $this->collection($templateTypeName);

        if (empty($collection)) {
            return null;
        }

        $barCodes = explode(',', $templateModel->getData('barcode_types'));

        if (is_object($collection)) {
            $source = $collection->getLastItem();
        } else {
            $source = end($collection);
        }

        $invoiceVariables = $this->defaultVariablesHelper
            ->setSourceType($source, $templateTypeName)
            ->getBarcodeDefault($barCodes);

        $result = $resultJson->setData($invoiceVariables);

        return $this->addResponse($result);
    }
}
