<?php
namespace WeSupply\Toolbox\Block;

class WeSupplyLinkV2 extends WeSupplyLink
{
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        if(!$this->helper->getWeSupplyEnabled() || !$this->helper->getDeliveryEstimationsHeaderLinkEnabled()){
            return;
        }


        return '<div class="wesupply-link-v2"><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml($this->getLabel()) . '</a></div>';
    }
}