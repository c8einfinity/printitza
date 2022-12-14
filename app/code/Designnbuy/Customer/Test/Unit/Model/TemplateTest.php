<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\Customer\Test\Unit\Model;

use Magento\Framework\App\TemplateTypesInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    /**
     * @var \Magento\Framework\View\DesignInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $design;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $registry;

    /**
     * @var \Magento\Store\Model\App\Emulation|\PHPUnit_Framework_MockObject_MockObject
     */
    private $appEmulation;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManager;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    private $store;

    /**
     * @var \Magento\Framework\View\Asset\Repository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $assetRepo;

    /**
     * @var \Magento\Framework\Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystem;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfig;

    /**
     * @var \Magento\Email\Model\Template\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $emailConfig;

    /**
     * @var \Magento\Email\Model\TemplateFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $templateFactory;

    /**
     * @var \Magento\Framework\Filter\FilterManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterManager;

    /**
     * @var \Magento\Framework\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    private $urlModel;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Designnbuy\Customer\Model\Template\FilterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterFactory;

    protected function setUp()
    {
        $this->context = $this->getMockBuilder('Magento\Framework\Model\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->design = $this->getMockBuilder('Magento\Framework\View\DesignInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->registry = $this->getMockBuilder('Magento\Framework\Registry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->appEmulation = $this->getMockBuilder('Magento\Store\Model\App\Emulation')
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeManager = $this->getMockBuilder('Magento\Store\Model\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->store = $this->getMockBuilder('Magento\Store\Model\Store')
            ->setMethods(['getFrontendName', 'getId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->store->expects($this->any())
            ->method('getFrontendName')
            ->will($this->returnValue('frontendName'));

        $this->store->expects($this->any())
            ->method('getFrontendName')
            ->will($this->returnValue('storeId'));

        $this->storeManager->expects($this->any())
            ->method('getStore')
            ->will($this->returnValue($this->store));

        $this->assetRepo = $this->getMockBuilder('Magento\Framework\View\Asset\Repository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filesystem = $this->getMockBuilder('Magento\Framework\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->emailConfig = $this->getMockBuilder('Magento\Email\Model\Template\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->templateFactory = $this->getMockBuilder('Magento\Email\Model\TemplateFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterManager = $this->getMockBuilder('Magento\Framework\Filter\FilterManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->urlModel = $this->getMockBuilder('Magento\Framework\Url')
            ->disableOriginalConstructor()
            ->getMock();

        $this->request = $this->getMockBuilder('Magento\Framework\App\RequestInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterFactory = $this->getMockBuilder('Designnbuy\Customer\Model\Template\FilterFactory')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Return the model under test with additional methods mocked.
     *
     * @param $mockedMethods array
     * @return \Designnbuy\Customer\Model\Template|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getModelMock(array $mockedMethods = [])
    {
        return $this->getMockBuilder('Designnbuy\Customer\Model\Template')
            ->setMethods(array_merge($mockedMethods, ['__wakeup', '__sleep', '_init']))
            ->setConstructorArgs(
                [
                    $this->context,
                    $this->design,
                    $this->registry,
                    $this->appEmulation,
                    $this->storeManager,
                    $this->assetRepo,
                    $this->filesystem,
                    $this->scopeConfig,
                    $this->emailConfig,
                    $this->templateFactory,
                    $this->filterManager,
                    $this->urlModel,
                    $this->request,
                    $this->filterFactory,
                ]
            )
            ->getMock();
    }

    public function testGetProcessedTemplateSubject()
    {
        $model = $this->getModelMock([
            'getTemplateFilter',
            'getDesignConfig',
            'applyDesignConfig',
            'setVariables',
        ]);

        $templateSubject = 'templateSubject';
        $model->setTemplateSubject($templateSubject);

        $filterTemplate = $this->getMockBuilder('Magento\Framework\Filter\Template')
            ->setMethods(['setVariables', 'setStoreId', 'filter'])
            ->disableOriginalConstructor()
            ->getMock();
        $model->expects($this->once())
            ->method('getTemplateFilter')
            ->will($this->returnValue($filterTemplate));

        $expectedResult = 'expected';
        $filterTemplate->expects($this->once())
            ->method('filter')
            ->with($templateSubject)
            ->will($this->returnValue($expectedResult));

        $variables = ['key' => 'value'];
        $filterTemplate->expects($this->once())
            ->method('setVariables')
            ->with(array_merge($variables, ['this' => $model]))
            ->will($this->returnValue($filterTemplate));

        $this->assertEquals($expectedResult, $model->getProcessedTemplateSubject($variables));
    }

    /**
     * This test is nearly identical to the
     * \Magento\Email\Test\Unit\Model\AbstractTemplateTest::testGetProcessedTemplate test, except this test also tests
     * to ensure that if a "design" variable is passed to method, the store ID from that object will be used for
     * filtering.
     *
     * @param $variables array
     * @param $templateType string
     * @param $storeId int
     * @param $expectedVariables array
     * @param $expectedResult string
     * @dataProvider getProcessedTemplateDataProvider
     */
    public function testGetProcessedTemplate($variables, $templateType, $storeId, $expectedVariables, $expectedResult)
    {
        $filterTemplate = $this->getMockBuilder('Designnbuy\Customer\Model\Template\Filter')
            ->setMethods([
                'setUseSessionInUrl',
                'setPlainTemplateMode',
                'setIsChildTemplate',
                'setDesignParams',
                'setVariables',
                'setStoreId',
                'filter',
                'getStoreId',
                'getInlineCssFiles',
            ])
            ->disableOriginalConstructor()
            ->getMock();

        $filterTemplate->expects($this->once())
            ->method('setUseSessionInUrl')
            ->with(false)
            ->will($this->returnSelf());
        $filterTemplate->expects($this->once())
            ->method('setPlainTemplateMode')
            ->with($templateType === TemplateTypesInterface::TYPE_TEXT)
            ->will($this->returnSelf());
        $filterTemplate->expects($this->once())
            ->method('setIsChildTemplate')
            ->will($this->returnSelf());
        $filterTemplate->expects($this->once())
            ->method('setDesignParams')
            ->will($this->returnSelf());
        $filterTemplate->expects($this->any())
            ->method('setStoreId')
            ->will($this->returnSelf());
        $filterTemplate->expects($this->any())
            ->method('getStoreId')
            ->will($this->returnValue($storeId));

        // The following block of code tests to ensure that the store id of the design will be used, if the
        // 'design' variable is set.
        $design = $this->getMockBuilder('Designnbuy\Customer\Model\Design')
            ->setMethods([
                'getStoreId',
            ])
            ->disableOriginalConstructor()
            ->getMock();
        $design->expects($this->once())
            ->method('getStoreId')
            ->will($this->returnValue('3'));
        $expectedVariables['design'] = $design;
        $variables['design'] = $design;

        $expectedVariables['store'] = $this->store;

        $model = $this->getModelMock([
            'getDesignParams',
            'applyDesignConfig',
            'getTemplateText',
            'isPlain',
        ]);
        $filterTemplate->expects($this->any())
            ->method('setVariables')
            ->with(array_merge(['this' => $model], $expectedVariables));
        $model->setTemplateFilter($filterTemplate);
        $model->setTemplateType($templateType);

        $designParams = [
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'theme' => 'themeId',
            'locale' => 'localeId',
        ];
        $model->expects($this->any())
            ->method('getDesignParams')
            ->will($this->returnValue($designParams));

        $model->expects($this->atLeastOnce())
            ->method('isPlain')
            ->will($this->returnValue($templateType === TemplateTypesInterface::TYPE_TEXT));

        $preparedTemplateText = $expectedResult; //'prepared text';
        $model->expects($this->once())
            ->method('getTemplateText')
            ->will($this->returnValue($preparedTemplateText));

        $filterTemplate->expects($this->once())
            ->method('filter')
            ->with($preparedTemplateText)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $model->getProcessedTemplate($variables));
    }

    /**
     * @return array
     */
    public function getProcessedTemplateDataProvider()
    {
        return [
            'default' => [
                'variables' => [],
                'templateType' => TemplateTypesInterface::TYPE_TEXT,
                'storeId' => 1,
                'expectedVariables' => [
                    'logo_url' => null,
                    'logo_alt' => 'frontendName',
                    'store' => null,
                    'logo_width' => null,
                    'logo_height' => null,
                    'store_phone' => null,
                    'store_hours' => null,
                    'store_email' => null,
                ],
                'expectedResult' => 'expected result',
            ],
            'logo variables set' => [
                'variables' => [
                    'logo_url' => 'http://example.com/logo',
                    'logo_alt' => 'Logo Alt',
                ],
                'templateType' => TemplateTypesInterface::TYPE_HTML,
                'storeId' => 1,
                'expectedVariables' => [
                    'logo_url' => 'http://example.com/logo',
                    'logo_alt' => 'Logo Alt',
                    'store' => null,
                    'logo_width' => null,
                    'logo_height' => null,
                    'store_phone' => null,
                    'store_hours' => null,
                    'store_email' => null,
                    'template_styles' => null,
                ],
                'expectedResult' => 'expected result',
            ],
        ];
    }
}
