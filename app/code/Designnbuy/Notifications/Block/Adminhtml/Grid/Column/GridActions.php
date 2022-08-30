<?php
namespace Designnbuy\Notifications\Block\Adminhtml\Grid\Column;

/**
 * Admin Notifications grid Actions
 */
class GridActions extends \Magento\Backend\Block\Widget\Grid\Column
{   
    const DESIGNIDEA_DESIGN = 1; 

    const TEMPLATE_DESIGN = 2;

    const TYPE_DESIGNER = 1;

    const TYPE_RESELLER = 2;

    const TYPE_DESIGN = 3;

    const TYPE_REPORT = 4;

    const TYPE_REDEEM = 5;

    const TYPE_UNPUBLISED = 6;
        
    const MARK_AS_READ = 1;

     public function __construct(
        //\Designnbuy\Designer\Model\DesignerFactory $designerFactory,
        \Magento\Framework\UrlInterface $url,
        \Designnbuy\Base\Helper\Data $baseHelper,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory
    ) {
       // $this->_designerFactory = $designerFactory;
        $this->_url = $url;
        $this->baseHelper = $baseHelper;
        $this->templateFactory = $templateFactory;
        $this->designideaFactory = $designideaFactory;
    }

    public function getCustomProductAttributeSetId()
    {
        return $this->baseHelper->getCustomProductAttributeSetId();
    }

    public function getCustomCanvasAttributeSetId()
    {
        return $this->baseHelper->getCustomCanvasAttributeSetId();
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     */
    public function getDesignIdeaId($typeId) {
        $design = $this->designideaFactory->create();
        if($typeId != ''){
            $design->load($typeId);
            return $design->getEntityId();
        }        
    }

    public function getTemplateId($typeId){
        $design = $this->templateFactory->create();
        if($typeId != ''){
            $design->load($typeId);
            return $design->getEntityId();
        }
    }
    
    public function getFrameCallback()
    {
        return [$this, 'decorateActions'];
    }

    public function decorateActions($value, $row, $column, $isExport)
    {
        $cell = null;
        $cell .= '<a href="'. $this->getViewUrl($row->getType(), $row->getTypeId(), $row->getProductType()).'">View</a> <br />';
        if ($row->getIsRead() == 0) {
            $cell .= '<a href="'. $this->getMarkAsReadUrl($row->getEntityId()) .'">Mark as Read</a><br />';
        }
        $cell .= '<a href="'. $this->getDeleteRecordUrl($row->getEntityId()) .'">Remove</a>';
        return $cell;
    }

    public function getDeleteRecordUrl($id) {
        return $this->_url->getUrl('notifications/notifications/deleteRecord/id/'.$id);

    }

    public function getMarkAsReadUrl($id) {
        return $this->_url->getUrl('notifications/notifications/setRead/id/'.$id);
    }

    public function getViewUrl($type, $typeId, $productType){
        switch ($type) {
           /* case self::TYPE_DESIGNER:
                $designerId = $this->getDesignerId($typeId);
                return $this->_url->getUrl('designer/designer/edit/id/'. $designerId);
                break;*/
            
            case self::TYPE_RESELLER:
                return $this->_url->getUrl('designnbuy_reseller/request/edit/request_id/'.$typeId);
                break;

            case self::TYPE_DESIGN:
                if($productType == self::DESIGNIDEA_DESIGN) {
                    $designId = $this->getDesignIdeaId($typeId);
                    return $this->_url->getUrl('designidea/designidea/edit/id/'.$designId);
                }
                if($productType == self::TEMPLATE_DESIGN) {
                    $designId = $this->getTemplateId($typeId);
                    return $this->_url->getUrl('template/template/edit/id/'.$designId);
                }
                break;

            case self::TYPE_REPORT:
                return $this->_url->getUrl();
                break;

            case self::TYPE_REDEEM:
                return $this->_url->getUrl('commission/redemption/edit/id/'.$typeId);
                break;

            case self::TYPE_UNPUBLISED:
                if($productType == self::DESIGNIDEA_DESIGN) {
                    $designId = $this->getDesignIdeaId($typeId);
                    return $this->_url->getUrl('designidea/designidea/edit/id/'.$designId);
                }
                if($productType == self::TEMPLATE_DESIGN) {
                    $designId = $this->getTemplateId($typeId);
                    return $this->_url->getUrl('template/template/edit/id/'.$designId);
                }
                break;
            default:
                break;
        }
    }

   /* public function getDesignerId($customerId) {
        $_designer = $this->_designerFactory->create();
        $_designer->load($customerId, 'customer_id');
        return $_designer->getEntityId();
    }*/
}
