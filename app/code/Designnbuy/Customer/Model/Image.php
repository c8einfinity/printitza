<?php

namespace Designnbuy\Customer\Model;

use Magento\Cron\Exception;
use Magento\Framework\Model\AbstractModel;

/**
 * Image Model
 *
 * @author      Ajay Makwana
 */
class Image extends AbstractModel
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Designnbuy\Customer\Model\ResourceModel\Image::class);
    }

}