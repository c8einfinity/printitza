<?php

namespace Designnbuy\CustomerPhotoAlbum\Model;

use Magento\Framework\Model\AbstractModel;


class Album extends AbstractModel
{
   protected function _construct()
	{
		$this->_init('Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album');
	}
    
}