<?php

namespace Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Photos;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\CustomerPhotoAlbum\Model\Photos', 'Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Photos');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>