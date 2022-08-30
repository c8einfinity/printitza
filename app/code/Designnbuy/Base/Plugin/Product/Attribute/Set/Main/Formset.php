<?php
namespace Designnbuy\Base\Plugin\Product\Attribute\Set\Main;

class Formset
{

    protected $baseHelper;

    public function __construct(
        \Designnbuy\Base\Helper\Data $baseHelper
    )
    {
        $this->baseHelper = $baseHelper;
    }

    /**
     * Get form HTML
     *
     * @return string
     */
    public function aroundGetFormHtml(
        \Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main\Formset $subject,
        \Closure $proceed
    )
    {
        $form = $subject->getForm();
        //echo "<pre>"; print_r(get_class_methods()); exit;
        
        
        if (is_object($form)) {
            
            if($subject->getRequest()->getParam('id') && !empty($this->baseHelper->getDefaultAttributeIds()))
            {
                if(in_array($subject->getRequest()->getParam('id'), $this->baseHelper->getDefaultAttributeIds())){
                    $form->setReadonly('set_name');
                    $subject->setForm($form);
                }
            }
        }

        return $proceed();
    }
}