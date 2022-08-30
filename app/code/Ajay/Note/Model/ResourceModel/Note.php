<?php


namespace Ajay\Note\Model\ResourceModel;

class Note extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('ajay_note_note', 'note_id');
    }
}
