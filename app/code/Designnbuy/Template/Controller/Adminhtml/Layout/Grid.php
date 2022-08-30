<?php

namespace Designnbuy\Template\Controller\Adminhtml\Layout;

/**
 * Template Grid action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Grid extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        return $this->_resultLayoutFactory->create();
    }
}
