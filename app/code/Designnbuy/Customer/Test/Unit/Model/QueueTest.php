<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Test\Unit\Model;

class QueueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Designnbuy\Customer\Model\Queue
     */
    protected $queue;

    /**
     * @var \Designnbuy\Customer\Model\Template\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateFilter;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $date;

    /**
     * @var \Designnbuy\Customer\Model\TemplateFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $templateFactory;

    /**
     * @var \Designnbuy\Customer\Model\ProblemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $problemFactory;

    /**
     * @var \Designnbuy\Customer\Model\ResourceModel\Design\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $designsCollection;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $designsCollectionFactory;

    /**
     * @var \Designnbuy\Customer\Model\Queue\TransportBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transportBuilder;

    /**
     * @var \Designnbuy\Customer\Model\ResourceModel\Queue|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->templateFilter = $this->getMockBuilder('\Designnbuy\Customer\Model\Template\Filter')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->date = $this->getMockBuilder('\Magento\Framework\Stdlib\DateTime\DateTime')
            ->disableOriginalConstructor()
            ->getMock();
        $this->templateFactory = $this->getMockBuilder('\Designnbuy\Customer\Model\TemplateFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create', 'load'])
            ->getMock();
        $this->problemFactory = $this->getMockBuilder('\Designnbuy\Customer\Model\ProblemFactory')
            ->disableOriginalConstructor()
            ->getMock();
        $this->transportBuilder = $this->getMockBuilder('\Designnbuy\Customer\Model\Queue\TransportBuilder')
            ->disableOriginalConstructor()
            ->setMethods(
                ['setTemplateData', 'setTemplateOptions', 'setTemplateVars', 'setFrom', 'addTo', 'getTransport']
            )
            ->getMock();
        $this->designsCollection =
            $this->getMockBuilder('\Designnbuy\Customer\Model\ResourceModel\Design\Collection')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource = $this->getMockBuilder('\Designnbuy\Customer\Model\ResourceModel\Queue')
            ->disableOriginalConstructor()
            ->getMock();
        $this->designsCollectionFactory = $this->getMockBuilder(
            '\Designnbuy\Customer\Model\ResourceModel\Design\CollectionFactory'
        )
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->designsCollectionFactory->expects($this->any())->method('create')->willReturn(
            $this->designsCollection
        );

        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->queue = $this->objectManager->getObject(
            '\Designnbuy\Customer\Model\Queue',
            [
                'templateFilter' => $this->templateFilter,
                'date' => $this->date,
                'templateFactory' => $this->templateFactory,
                'problemFactory' => $this->problemFactory,
                'designCollectionFactory' => $this->designsCollectionFactory,
                'transportBuilder' => $this->transportBuilder,
                'resource' => $this->resource
            ]
        );
    }

    public function testSendPerDesign1()
    {
        $this->queue->setQueueStatus(2);
        $this->queue->setQueueStartAt(1);

        $this->assertEquals($this->queue, $this->queue->sendPerDesign());
    }

    public function testSendPerDesignZeroSize()
    {
        $this->queue->setQueueStatus(1);
        $this->queue->setQueueStartAt(1);
        $this->designsCollection->expects($this->once())->method('getQueueJoinedFlag')->willReturn(false);
        $this->designsCollection->expects($this->once())->method('useQueue')->with($this->queue)->willReturnSelf();
        $this->designsCollection->expects($this->once())->method('getSize')->willReturn(0);
        $this->date->expects($this->once())->method('gmtDate')->willReturn('any_date');

        $this->assertEquals($this->queue, $this->queue->sendPerDesign());
    }

    public function testSendPerDesign2()
    {
        $this->queue->setQueueStatus(1);
        $this->queue->setQueueStartAt(1);
        $collection = $this->getMockBuilder('\Magento\Framework\Data\Collection')
            ->disableOriginalConstructor()
            ->setMethods(['getItems'])
            ->getMock();
        $item = $this->getMockBuilder('\Designnbuy\Customer\Model\Design')
            ->disableOriginalConstructor()
            ->setMethods(['getStoreId', 'getDesignEmail', 'getDesignFullName', 'received'])
            ->getMock();
        $transport = $this->getMock('\Magento\Framework\Mail\TransportInterface');
        $this->designsCollection->expects($this->once())->method('getQueueJoinedFlag')->willReturn(false);
        $this->designsCollection->expects($this->once())->method('useQueue')->with($this->queue)->willReturnSelf();
        $this->designsCollection->expects($this->once())->method('getSize')->willReturn(5);
        $this->designsCollection->expects($this->once())->method('useOnlyUnsent')->willReturnSelf();
        $this->designsCollection->expects($this->once())->method('showCustomerInfo')->willReturnSelf();
        $this->designsCollection->expects($this->once())->method('setPageSize')->willReturnSelf();
        $this->designsCollection->expects($this->once())->method('setCurPage')->willReturnSelf();
        $this->designsCollection->expects($this->once())->method('load')->willReturn($collection);
        $this->transportBuilder->expects($this->once())->method('setTemplateData')->willReturnSelf();
        $collection->expects($this->atLeastOnce())->method('getItems')->willReturn([$item]);
        $item->expects($this->once())->method('getStoreId')->willReturn('store_id');
        $item->expects($this->once())->method('getDesignEmail')->willReturn('email');
        $item->expects($this->once())->method('getDesignFullName')->willReturn('full_name');
        $this->transportBuilder->expects($this->once())->method('setTemplateOptions')->willReturnSelf();
        $this->transportBuilder->expects($this->once())->method('setTemplateVars')->willReturnSelf();
        $this->transportBuilder->expects($this->once())->method('setFrom')->willReturnSelf();
        $this->transportBuilder->expects($this->once())->method('addTo')->willReturnSelf();
        $this->transportBuilder->expects($this->once())->method('getTransport')->willReturn($transport);
        $item->expects($this->once())->method('received')->with($this->queue)->willReturnSelf();

        $this->assertEquals($this->queue, $this->queue->sendPerDesign());
    }

    public function testGetDataForSave()
    {
        $result = [
            'template_id' => 'id',
            'queue_status' => 'status',
            'queue_start_at' => 'start_at',
            'queue_finish_at' => 'finish_at'
        ];
        $this->queue->setTemplateId('id');
        $this->queue->setQueueStatus('status');
        $this->queue->setQueueStartAt('start_at');
        $this->queue->setQueueFinishAt('finish_at');

        $this->assertEquals($result, $this->queue->getDataForSave());
    }

    public function testGetTemplate()
    {
        $template = $this->getMockBuilder('\Designnbuy\Customer\Model\Template')
            ->disableOriginalConstructor()
            ->getMock();
        $this->queue->setTemplateId(2);
        $this->templateFactory->expects($this->once())->method('create')->willReturn($template);
        $template->expects($this->once())->method('load')->with(2)->willReturnSelf();

        $this->assertEquals($template, $this->queue->getTemplate());
    }

    public function testGetStores()
    {
        $stores = ['store'];
        $this->resource->expects($this->once())->method('getStores')->willReturn($stores);

        $this->assertEquals($stores, $this->queue->getStores());
    }
}
