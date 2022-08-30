<?php
/**
 * Created by PhpStorm.
 * User: Ajay
 * Date: 31-07-2018
 * Time: 15:21
 */

namespace Designnbuy\Font\Block\Adminhtml\Font\Edit\Tab;

class Help extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'font/help.phtml';

    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context  $context
     * @param array                                    $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}