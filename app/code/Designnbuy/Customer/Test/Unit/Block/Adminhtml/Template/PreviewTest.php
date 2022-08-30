<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Test\Unit\Block\Adminhtml\Template;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;

class PreviewTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Designnbuy\Customer\Block\Adminhtml\Template\Preview */
    protected $preview;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Designnbuy\Customer\Model\Template|\PHPUnit_Framework_MockObject_MockObject */
    protected $template;

    /** @var \Designnbuy\Customer\Model\DesignFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $designFactory;

    /** @var \Magento\Framework\App\State|\PHPUnit_Framework_MockObject_MockObject */
    protected $appState;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    protected function setUp()
    {
        $this->request = $this->getMock('Magento\Framework\App\RequestInterface', [], [], '', false);
        $this->appState = $this->getMock('Magento\Framework\App\State', [], [], '', false);
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface', [], [], '', false);
        $this->template = $this->getMock(
            'Designnbuy\Customer\Model\Template',
            [
                'setTemplateType',
                'setTemplateText',
                'setTemplateStyles',
                'isPlain',
                'emulateDesign',
                'revertDesign',
                'getProcessedTemplate',
                'load'
            ],
            [],
            '',
            false
        );
        $templateFactory = $this->getMock('Designnbuy\Customer\Model\TemplateFactory', ['create'], [], '', false);
        $templateFactory->expects($this->once())->method('create')->willReturn($this->template);
        $this->designFactory = $this->getMock(
            'Designnbuy\Customer\Model\DesignFactory',
            ['create'],
            [],
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->preview = $this->objectManagerHelper->getObject(
            'Designnbuy\Customer\Block\Adminhtml\Template\Preview',
            [
                'appState' => $this->appState,
                'storeManager' => $this->storeManager,
                'request' => $this->request,
                'templateFactory' => $templateFactory,
                'designFactory' => $this->designFactory
            ]
        );
    }

    public function testToHtml()
    {
        $this->request->expects($this->any())->method('getParam')->willReturnMap(
            [
                ['id', null, 1],
                ['store', null, 1]
            ]
        );

        $this->template->expects($this->atLeastOnce())->method('emulateDesign')->with(1);
        $this->template->expects($this->atLeastOnce())->method('revertDesign');

        $this->appState->expects($this->atLeastOnce())->method('emulateAreaCode')
            ->with(
                \Designnbuy\Customer\Model\Template::DEFAULT_DESIGN_AREA,
                [$this->template, 'getProcessedTemplate'],
                [['design' => null]]
            )
            ->willReturn('Processed Template');

        $this->assertEquals('Processed Template', $this->preview->toHtml());
    }

    public function testToHtmlForNewTemplate()
    {
        $this->request->expects($this->any())->method('getParam')->willReturnMap(
            [
                ['type', null, TemplateTypesInterface::TYPE_TEXT],
                ['text', null, 'Processed Template'],
                ['styles', null, '.class-name{color:red;}']
            ]
        );

        $this->template->expects($this->once())->method('setTemplateType')->with(TemplateTypesInterface::TYPE_TEXT)
            ->willReturnSelf();
        $this->template->expects($this->once())->method('setTemplateText')->with('Processed Template')
            ->willReturnSelf();
        $this->template->expects($this->once())->method('setTemplateStyles')->with('.class-name{color:red;}')
            ->willReturnSelf();
        $this->template->expects($this->atLeastOnce())->method('isPlain')->willReturn(true);
        $this->template->expects($this->atLeastOnce())->method('emulateDesign')->with(1);
        $this->template->expects($this->atLeastOnce())->method('revertDesign');

        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $store->expects($this->atLeastOnce())->method('getId')->willReturn(1);

        $this->storeManager->expects($this->atLeastOnce())->method('getStores')->willReturn([$store]);


        $this->appState->expects($this->atLeastOnce())->method('emulateAreaCode')
            ->with(
                \Designnbuy\Customer\Model\Template::DEFAULT_DESIGN_AREA,
                [
                    $this->template,
                    'getProcessedTemplate'
                ],
                [
                    [
                        'design' => null
                    ]
                ]
            )
            ->willReturn('Processed Template');

        $this->assertEquals('<pre>Processed Template</pre>', $this->preview->toHtml());
    }

    public function testToHtmlWithDesign()
    {
        $this->request->expects($this->any())->method('getParam')->willReturnMap(
            [
                ['id', null, 2],
                ['store', null, 1],
                ['design', null, 3]
            ]
        );
        $design = $this->getMock('Designnbuy\Customer\Model\Design', [], [], '', false);
        $design->expects($this->atLeastOnce())->method('load')->with(3)->willReturnSelf();
        $this->designFactory->expects($this->atLeastOnce())->method('create')->willReturn($design);

        $this->template->expects($this->atLeastOnce())->method('emulateDesign')->with(1);
        $this->template->expects($this->atLeastOnce())->method('revertDesign');

        $this->appState->expects($this->atLeastOnce())->method('emulateAreaCode')
            ->with(
                \Designnbuy\Customer\Model\Template::DEFAULT_DESIGN_AREA,
                [
                    $this->template,
                    'getProcessedTemplate'
                ],
                [
                    [
                        'design' => $design
                    ]
                ]
            )
            ->willReturn('Processed Template');

        $this->assertEquals('Processed Template', $this->preview->toHtml());
    }
}
