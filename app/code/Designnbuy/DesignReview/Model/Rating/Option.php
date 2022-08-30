<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Model\Rating;

/**
 * Rating option model
 *
 * @api
 * @method int getRatingId()
 * @method \Designnbuy\DesignReview\Model\Rating\Option setRatingId(int $value)
 * @method string getCode()
 * @method \Designnbuy\DesignReview\Model\Rating\Option setCode(string $value)
 * @method int getValue()
 * @method \Designnbuy\DesignReview\Model\Rating\Option setValue(int $value)
 * @method int getPosition()
 * @method \Designnbuy\DesignReview\Model\Rating\Option setPosition(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @codeCoverageIgnore
 * @since 100.0.2
 */
class Option extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Designnbuy\DesignReview\Model\ResourceModel\Rating\Option::class);
    }

    /**
     * @return $this
     */
    public function addVote()
    {
        $this->getResource()->addVote($this);
        return $this;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setOptionId($id);
        return $this;
    }
}
