<?php
namespace Designnbuy\CustomerPhotoAlbum\Model\ResourceModel;

class Photos extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_customer_album_photos', 'photo_id');
    }
}
?>