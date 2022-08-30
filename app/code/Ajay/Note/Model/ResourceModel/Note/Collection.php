<?php


namespace Ajay\Note\Model\ResourceModel\Note;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Ajay\Note\Model\Note',
            'Ajay\Note\Model\ResourceModel\Note'
        );
    }
}
