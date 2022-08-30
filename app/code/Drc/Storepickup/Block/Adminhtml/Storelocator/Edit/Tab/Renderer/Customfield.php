<?php
/**
 * @author   Drc Systems India Pvt Ltd.
 */

namespace Drc\Storepickup\Block\Adminhtml\Storelocator\Edit\Tab\Renderer;
 
class Customfield extends \Magento\Backend\Block\Widget\Form\Renderer\Fieldset\Element implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    protected $_template = 'Drc_Storepickup::customfield.phtml';
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
         $this->_element = $element;
         $html = $this->toHtml();
         return $html;
    }
}
