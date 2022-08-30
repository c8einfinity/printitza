<?php
namespace Designnbuy\Productattach\Block\Adminhtml\Productattach\Renderer;
 
use Magento\Framework\DataObject;

/**
 * Class FileIcon
 * @package Designnbuy\Productattach\Block\Adminhtml\Productattach\Renderer
 */
class FileIcon extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    private $assetRepo;
    
    /**
     * @var \Designnbuy\Productattach\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Designnbuy\Productattach\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Designnbuy\Productattach\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->assetRepo = $assetRepo;
    }
 
    /**
     * get customer group name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        $file = $row->getFile();
        $fileExt = pathinfo($file, PATHINFO_EXTENSION);
        if ($fileExt) {
            $iconImage = $this->assetRepo->getUrl('Designnbuy_Productattach::images/'.$fileExt.'.png');
            $fileIcon = "<a href=".$this->dataHelper->getBaseUrl().'/'.$file." target='_blank'>
            <img src='".$iconImage."' /></a>";
        } else {
            $iconImage = $this->assetRepo->getUrl('Designnbuy_Productattach::images/unknown.png');
            $fileIcon = "<img src='".$iconImage."' />";
        }
        
        return $fileIcon;
    }
}
