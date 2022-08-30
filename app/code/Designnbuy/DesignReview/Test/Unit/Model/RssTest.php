<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\DesignReview\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class RssTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Designnbuy\DesignReview\Model\Rss
     */
    protected $rss;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $managerInterface;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $reviewFactory;

    protected function setUp()
    {
        $this->managerInterface = $this->createMock(\Magento\Framework\Event\ManagerInterface::class);
        $this->reviewFactory = $this->createPartialMock(\Designnbuy\DesignReview\Model\ReviewFactory::class, ['create']);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->rss = $this->objectManagerHelper->getObject(
            \Designnbuy\DesignReview\Model\Rss::class,
            [
                'eventManager' => $this->managerInterface,
                'reviewFactory' => $this->reviewFactory
            ]
        );
    }

    public function testGetProductCollection()
    {
        $reviewModel = $this->createPartialMock(\Designnbuy\DesignReview\Model\Review::class, [
                '__wakeUp',
                'getProductCollection'
            ]);
        $productCollection = $this->createPartialMock(
            \Designnbuy\DesignReview\Model\ResourceModel\Review\Product\Collection::class,
            [
                'addStatusFilter',
                'addAttributeToSelect',
                'setDateOrder'
            ]
        );
        $reviewModel->expects($this->once())->method('getProductCollection')
            ->will($this->returnValue($productCollection));
        $this->reviewFactory->expects($this->once())->method('create')->will($this->returnValue($reviewModel));
        $productCollection->expects($this->once())->method('addStatusFilter')->will($this->returnSelf());
        $productCollection->expects($this->once())->method('addAttributeToSelect')->will($this->returnSelf());
        $productCollection->expects($this->once())->method('setDateOrder')->will($this->returnSelf());
        $this->managerInterface->expects($this->once())->method('dispatch')->will($this->returnSelf());
        $this->assertEquals($productCollection, $this->rss->getProductCollection());
    }
}
