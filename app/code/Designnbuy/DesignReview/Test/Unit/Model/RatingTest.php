<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Designnbuy\DesignReview\Model\Review;
use Designnbuy\DesignReview\Model\Rating;

class RatingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Designnbuy\DesignReview\Model\Rating
     */
    private $rating;

    /**
     * Init objects needed by tests
     */
    protected function setUp()
    {
        $helper = new ObjectManager($this);
        $this->rating = $helper->getObject(Rating::class);
    }

    /**
     * @covers \Designnbuy\DesignReview\Model\Rating::getIdentities()
     * @return void
     */
    public function testGetIdentities()
    {
        static::assertEquals([Review::CACHE_TAG], $this->rating->getIdentities());
    }
}
