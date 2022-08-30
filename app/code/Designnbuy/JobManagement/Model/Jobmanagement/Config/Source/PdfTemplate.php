<?php
namespace Designnbuy\JobManagement\Model\Jobmanagement\Config\Source;
use Eadesigndev\PdfGeneratorPro\Model\ResourceModel\Pdfgenerator\Grid\CollectionFactory as PDFCollectionFactory;
class PdfTemplate implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $options;
    /**
     * @var \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory
     */
    protected $_pdfCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $userRolesFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        PDFCollectionFactory $pdfCollectionFactory
    ) {
        $this->_pdfCollectionFactory = $pdfCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_pdfCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        $collection->getSelect()->order('template_id', 'DESC');
        if ($this->options === null) 
        {
            $this->options = [['label' => __('Please select'), 'value' => 0]];
            foreach ($collection as $status) {
                $this->options[] = ['value' => $status['template_id'], 'label' => $status['template_name'],];
            }        
        }
        return $this->options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
    */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}   
