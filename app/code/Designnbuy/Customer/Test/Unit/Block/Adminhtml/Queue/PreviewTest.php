<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Test\Unit\Block\Adminhtml\Queue;

class PreviewTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Designnbuy\Customer\Model\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $template;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $request;

    /**
     * @var \Designnbuy\Customer\Model\Design|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $design;

    /**
     * @var \Designnbuy\Customer\Model\Queue|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $queue;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Designnbuy\Customer\Block\Adminhtml\Queue\Preview
     */
    protected $preview;

    protected function setUp()
    {
        $context = $this->getMock('Magento\Backend\Block\Template\Context', [], [], '', false);
        $eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface', [], [], '', false);
        $context->expects($this->once())->method('getEventManager')->will($this->returnValue($eventManager));
        $scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface', [], [], '', false);
        $context->expects($this->once())->method('getScopeConfig')->will($this->returnValue($scopeConfig));
        $this->request = $this->getMock('Magento\Framework\App\Request\Http', [], [], '', false);
        $context->expects($this->once())->method('getRequest')->will($this->returnValue($this->request));
        $this->storeManager = $this->getMock(
            'Magento\Store\Model\StoreManager',
            ['getStores', 'getDefaultStoreView'],
            [],
            '',
            false
        );
        $context->expects($this->once())->method('getStoreManager')->will($this->returnValue($this->storeManager));
        $appState = $this->getMock('Magento\Framework\App\State', [], [], '', false);
        $context->expects($this->once())->method('getAppState')->will($this->returnValue($appState));

        $backendSession = $this->getMockBuilder('Magento\Backend\Model\Session')
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->once())->method('getBackendSession')->willReturn($backendSession);

        $templateFactory = $this->getMock('Designnbuy\Customer\Model\TemplateFactory', ['create'], [], '', false);
        $this->template = $this->getMock('Designnbuy\Customer\Model\Template', [], [], '', false);
        $templateFactory->expects($this->once())->method('create')->will($this->returnValue($this->template));

        $designFactory = $this->getMock('Designnbuy\Customer\Model\DesignFactory', ['create'], [], '', false);
        $this->design = $this->getMock('Designnbuy\Customer\Model\Design', [], [], '', false);
        $designFactory->expects($this->once())->method('create')->will($this->returnValue($this->design));

        $queueFactory = $this->getMock('Designnbuy\Customer\Model\QueueFactory', ['create'], [], '', false);
        $this->queue = $this->getMock('Designnbuy\Customer\Model\Queue', ['load'], [], '', false);
        $queueFactory->expects($this->any())->method('create')->will($this->returnValue($this->queue));

        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->preview = $this->objectManager->getObject(
            'Designnbuy\Customer\Block\Adminhtml\Queue\Preview',
            [
                'context' => $context,
                'templateFactory' => $templateFactory,
                'designFactory' => $designFactory,
                'queueFactory' => $queueFactory,
            ]
        );
    }

    public function testToHtmlEmpty()
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->getMock('Magento\Store\Model\Store', ['getId'], [], '', false);
        $this->storeManager->expects($this->once())->method('getDefaultStoreView')->will($this->returnValue($store));
        $result = $this->preview->toHtml();
        $this->assertEquals('', $result);
    }

    public function testToHtmlWithId()
    {
        $this->request->expects($this->any())->method('getParam')->will($this->returnValueMap(
            [
                ['id', null, 1],
                ['store_id', null, 0]
            ]
        ));
        $this->queue->expects($this->once())->method('load')->will($this->returnSelf());
        $this->template->expects($this->any())->method('isPlain')->will($this->returnValue(true));
        /** @var \Magento\Store\Model\Store $store */
        $this->storeManager->expects($this->once())->method('getDefaultStoreView')->will($this->returnValue(null));
        $store = $this->getMock('Magento\Store\Model\Store', ['getId'], [], '', false);
        $this->storeManager->expects($this->once())->method('getStores')->will($this->returnValue([0 => $store]));
        $result = $this->preview->toHtml();
        $this->assertEquals('<pre></pre>', $result);
    }
}
