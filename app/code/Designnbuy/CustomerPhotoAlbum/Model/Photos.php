<?php
namespace Designnbuy\CustomerPhotoAlbum\Model;

class Photos extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Photos');
    }
}
?>