<?php

namespace Unific\Connector\Block\Adminhtml\Log\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('log_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Log Information'));
    }

    /**
     * @return mixed
     */
    protected function _beforeToHtml()
    {

        $this->addTab(
            'server_info',
            [
                'label' => __('General'),
                'title' => __('General'),
                'content' => $this->getLayout()->createBlock(
                    \Unific\Connector\Block\Adminhtml\Log\Edit\Tab\Info::class
                )->toHtml(),
                'active' => true
            ]
        );

        return parent::_beforeToHtml();
    }
}
