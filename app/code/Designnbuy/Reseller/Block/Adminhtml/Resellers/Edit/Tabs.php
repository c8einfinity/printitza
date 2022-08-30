<?php
namespace Designnbuy\Reseller\Block\Adminhtml\Resellers\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
   public function __construct(
        \Magento\Framework\App\Request\Http $request,
        Context $context,
        Registry $coreRegistry,
        EncoderInterface $jsonEncoder,
        Session $authSession,
        array $data = []
    )
    {
        $this->request = $request;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
    }

    protected function _construct()
    {
		parent::_construct();
        $this->setId('designnbuy_resellers_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Reseller Information'));
    }

    /**
     * before render html
     * @access protected
     * @return Designnbuy_Customermanagement_Block_Adminhtml_Customer_Edit_Tabs
     * @author Ultimate Module Creator
     */
    protected function _beforeToHtml()
    {
        $reseller = $this->getReseller();
        $importProducts = $this->getRequest()->getParam('import_id');
        if($this->request->getFullActionName() == 'designnbuy_reseller_resellers_importproducts'):
            $this->addTab(
                'reseller_productpool',
                [
                    'label' => __('Select Product Pool'),
                    'url' => $this->getUrl('designnbuy_reseller/resellers/productpools', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
        endif;
        $resellerId = $this->getRequest()->getParam('reseller_id');
        if($resellerId != "" && $importProducts == ""):
            $this->addTab('reseller_info',
                [
                    'label' => __('E-Commerce Settings'),
                    'content' => $this->getLayout()->createBlock(
                        \Designnbuy\Reseller\Block\Adminhtml\Resellers\Edit\Tab\Info::class
                    )->toHtml()
                ]
            );
        endif;

        /*if($reseller && $reseller->getId())
        {
            $importProducts = $this->getRequest()->getParam('importproducts');
            if($this->request->getFullActionName() == 'designnbuy_reseller_resellers_importproducts'):
                $this->addTab(
                    'reseller_productpool',
                    [
                        'label' => __('Select Product Pool'),
                        'url' => $this->getUrl('designnbuy_reseller/resellers/productpools', ['_current' => true]),
                        'class' => 'ajax'
                    ]
                );
            else:
                $this->addTab('reseller_info',
                    [
                        'label' => __('E-Commerce Settings'),
                        'content' => $this->getLayout()->createBlock(
                            \Designnbuy\Reseller\Block\Adminhtml\Resellers\Edit\Tab\Info::class
                        )->toHtml()
                    ]
                );
            endif;
        }*/
        return parent::_beforeToHtml();
    }

    public function getReseller()
    {
        return $this->coreRegistry->registry('resellers');
    }
}