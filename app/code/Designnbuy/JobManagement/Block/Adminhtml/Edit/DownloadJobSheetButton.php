<?php

namespace Designnbuy\JobManagement\Block\Adminhtml\Edit;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
/**
 * Class DownloadJobSheetButton
 */
class DownloadJobSheetButton extends GenericButton implements ButtonProviderInterface
{
    private $_helper;
    
    const XML_PATH_GENERAL_PDF_TEMPLATE  = 'jobmanagement/settings/general_pdf_template';
    const XML_PATH_ONLY_PAYMENT_PDF_TEMPLATE  = 'jobmanagement/settings/only_payment_pdf_template';
    const XML_PATH_ONLY_CUSTOMER_PDF_TEMPLATE  = 'jobmanagement/settings/only_customer_pdf_template';
    const XML_PATH_ONLY_DOWNLOAD_PDF_TEMPLATE  = 'jobmanagement/settings/only_download_pdf_template';
    const XML_PATH_CUSTOMER_AND_PAYMENT_PDF_TEMPLATE  = 'jobmanagement/settings/customer_and_payment_pdf_template';
    const XML_PATH_CUSTOMER_AND_DOWNLOAD_PDF_TEMPLATE  = 'jobmanagement/settings/customer_and_download_pdf_template';
    const XML_PATH_PAYMENT_AND_DOWNLOAD_PDF_TEMPLATE  = 'jobmanagement/settings/payment_and_download_pdf_template';

    /**
     * @var \Designnbuy\Workflow\Helper\Data
     */
    protected $workflowData;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        UrlInterface $urlBuilder,
        \Designnbuy\JobManagement\Helper\Data $helper,
        \Designnbuy\Workflow\Helper\Data $workflowData
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->workflowData = $workflowData;
        $this->_urlBuilder = $urlBuilder;
    }
    /**
     * @return array
     */
    public function getButtonData()
    {
        /*if($this->workflowData->getWorkflowUser()) {
            $workFlowRole = $this->workflowData->getWorkflowUserRole();
            $accesses = $workFlowRole->getAccesses();
            if(isset($accesses) && !empty($accesses) && !in_array('download_files',$accesses)){
                return;
            }
        }*/
        $entityId = $this->getEntityId();
        $job = $this->_helper->getJobContent($entityId);
        $jobTitle = $job->getTitle();
        if(isset($jobTitle) && $jobTitle != ""){
            $disabledClass = "";
        }else{
            $disabledClass = "disabled";
        }
        
        return [
            'label' => __('Download Job Sheet'),
            'class' => ''.$disabledClass.' create-ticket',
            'on_click' => 'setLocation(\'' . $this->downloadJobSheetLink() . '\')',
            'sort_order' => 80,
        ];
    }

    public function getPdfTemplateLink(){
        if($this->workflowData->getWorkflowUser()) 
        {
            $workFlowRole = $this->workflowData->getWorkflowUserRole();
            $accesses = $workFlowRole->getAccesses();            
            $pdfTemplate = "";
            
            ## Omly Customer View PDF
            if(isset($accesses) && !empty($accesses) && 
                in_array('view_customer_details' ,$accesses) && 
                !in_array('view_payment_details' ,$accesses) && 
                !in_array('download_files' ,$accesses)
            ){
                $pdfTemplate = $this->_helper->_getConfigValue(self::XML_PATH_ONLY_CUSTOMER_PDF_TEMPLATE);
            }

            ## Omly Payment View PDF
            if(isset($accesses) && !empty($accesses) && 
                in_array('view_payment_details' ,$accesses) && 
                !in_array('view_customer_details' ,$accesses) && 
                !in_array('download_files' ,$accesses)
            ){
                $pdfTemplate = $this->_helper->_getConfigValue(self::XML_PATH_ONLY_PAYMENT_PDF_TEMPLATE);
            }

            ## Omly Download View PDF
            if(isset($accesses) && !empty($accesses) && 
                in_array('download_files' ,$accesses) && 
                !in_array('view_payment_details' ,$accesses) && 
                !in_array('view_customer_details' ,$accesses)
            ){
                $pdfTemplate = $this->_helper->_getConfigValue(self::XML_PATH_ONLY_DOWNLOAD_PDF_TEMPLATE);
            }

            ## Customer & Payment View PDF
            if(isset($accesses) && !empty($accesses) && 
                in_array('view_payment_details' ,$accesses) && 
                in_array('view_customer_details' ,$accesses) && 
                !in_array('download_files' ,$accesses)
            ){
                $pdfTemplate = $this->_helper->_getConfigValue(self::XML_PATH_CUSTOMER_AND_PAYMENT_PDF_TEMPLATE);
            }

            ## Payment & Download Option
            if(isset($accesses) && !empty($accesses) && 
                in_array('view_payment_details' ,$accesses) && 
                in_array('download_files' ,$accesses) && 
                !in_array('view_customer_details' ,$accesses)
            ){ 
                $pdfTemplate = $this->_helper->_getConfigValue(self::XML_PATH_PAYMENT_AND_DOWNLOAD_PDF_TEMPLATE);
            }

            ## Customer & Download Option
            if(isset($accesses) && !empty($accesses) && 
                in_array('view_customer_details' ,$accesses) && 
                in_array('download_files' ,$accesses) && 
                !in_array('view_payment_details' ,$accesses)
            ){
                $pdfTemplate = $this->_helper->_getConfigValue(self::XML_PATH_CUSTOMER_AND_DOWNLOAD_PDF_TEMPLATE);
            }

            ## Customer, Download & Payment Option
            if(isset($accesses) && !empty($accesses) && 
                in_array('download_files' ,$accesses) && 
                in_array('view_payment_details',$accesses) && 
                in_array('view_customer_details' ,$accesses)
            ){
                $pdfTemplate = $this->_helper->_getConfigValue(self::XML_PATH_GENERAL_PDF_TEMPLATE);
            }
        }else{
            $pdfTemplate = $this->_helper->_getConfigValue(self::XML_PATH_GENERAL_PDF_TEMPLATE);
        }
        return $pdfTemplate;
    }

    public function downloadJobSheetLink(){
        $pdfTemplate = $this->getPdfTemplateLink();
        return  $this->_urlBuilder->getUrl(
            'eadesign_pdf/order/printpdf',
            [
                'order_id' => $this->getOrderId(), 
                'template_id' => $pdfTemplate,
                'job_id' => $this->getEntityId()
            ]
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
