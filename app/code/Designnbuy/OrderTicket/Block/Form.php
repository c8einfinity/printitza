<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * ORDERTICKET Item Dynamic attributes Form Block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\OrderTicket\Block;

class Form extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Name of the block in layout update xml file
     *
     * @var string
     */
    protected $_xmlBlockName = 'designnbuy_orderticket_item_form_template';

    /**
     * Class path of Form Model
     *
     * @var string
     */
    protected $_formModelPath = 'Designnbuy\OrderTicket\Model\Item\Form';
}
