<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Test\Unit\Model;

class DesignTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Designnbuy\Customer\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerData;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerAccountManagement;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $inlineTranslation;

    /**
     * @var \Designnbuy\Customer\Model\ResourceModel\Design|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Designnbuy\Customer\Model\Design
     */
    protected $design;

    protected function setUp()
    {
        $this->customerData = $this->getMock('Designnbuy\Customer\Helper\Data', [], [], '', false);
        $this->scopeConfig = $this->getMock('Magento\Framework\App\Config\ScopeConfigInterface');
        $this->transportBuilder = $this->getMock(
            'Magento\Framework\Mail\Template\TransportBuilder',
            [
                'setTemplateIdentifier',
                'setTemplateOptions',
                'setTemplateVars',
                'setFrom',
                'addTo',
                'getTransport'
            ],
            [],
            '',
            false
        );
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->customerSession = $this->getMock(
            'Magento\Customer\Model\Session',
            [
                'isLoggedIn',
                'getCustomerDataObject',
                'getCustomerId'
            ],
            [],
            '',
            false
        );
        $this->customerRepository = $this->getMock('Magento\Customer\Api\CustomerRepositoryInterface');
        $this->customerAccountManagement = $this->getMock('Magento\Customer\Api\AccountManagementInterface');
        $this->inlineTranslation = $this->getMock('Magento\Framework\Translate\Inline\StateInterface');
        $this->resource = $this->getMock(
            'Designnbuy\Customer\Model\ResourceModel\Design',
            [
                'loadByEmail',
                'getIdFieldName',
                'save',
                'loadByCustomerData',
                'received'
            ],
            [],
            '',
            false
        );
        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->design = $this->objectManager->getObject(
            'Designnbuy\Customer\Model\Design',
            [
                'customerData' => $this->customerData,
                'scopeConfig' => $this->scopeConfig,
                'transportBuilder' => $this->transportBuilder,
                'storeManager' => $this->storeManager,
                'customerSession' => $this->customerSession,
                'customerRepository' => $this->customerRepository,
                'customerAccountManagement' => $this->customerAccountManagement,
                'inlineTranslation' => $this->inlineTranslation,
                'resource' => $this->resource
            ]
        );
    }

    public function testSubscribe()
    {
        $email = 'design_email@magento.com';
        $this->resource->expects($this->any())->method('loadByEmail')->willReturn(
            [
                'design_status' => 3,
                'design_email' => $email,
                'name' => 'design_name'
            ]
        );
        $this->scopeConfig->expects($this->any())->method('getValue')->willReturn(true);
        $this->customerSession->expects($this->any())->method('isLoggedIn')->willReturn(true);
        $customerDataModel = $this->getMock('\Magento\Customer\Api\Data\CustomerInterface');
        $this->customerSession->expects($this->any())->method('getCustomerDataObject')->willReturn($customerDataModel);
        $this->customerSession->expects($this->any())->method('getCustomerId')->willReturn(1);
        $customerDataModel->expects($this->any())->method('getEmail')->willReturn($email);
        $this->customerRepository->expects($this->any())->method('getById')->willReturn($customerDataModel);
        $customerDataModel->expects($this->any())->method('getStoreId')->willReturn(1);
        $customerDataModel->expects($this->any())->method('getId')->willReturn(1);
        $this->sendEmailCheck();
        $this->resource->expects($this->atLeastOnce())->method('save')->willReturnSelf();

        $this->assertEquals(1, $this->design->subscribe($email));
    }

    public function testSubscribeNotLoggedIn()
    {
        $email = 'design_email@magento.com';
        $this->resource->expects($this->any())->method('loadByEmail')->willReturn(
            [
                'design_status' => 3,
                'design_email' => $email,
                'name' => 'design_name'
            ]
        );
        $this->scopeConfig->expects($this->any())->method('getValue')->willReturn(true);
        $this->customerSession->expects($this->any())->method('isLoggedIn')->willReturn(false);
        $customerDataModel = $this->getMock('\Magento\Customer\Api\Data\CustomerInterface');
        $this->customerSession->expects($this->any())->method('getCustomerDataObject')->willReturn($customerDataModel);
        $this->customerSession->expects($this->any())->method('getCustomerId')->willReturn(1);
        $customerDataModel->expects($this->any())->method('getEmail')->willReturn($email);
        $this->customerRepository->expects($this->any())->method('getById')->willReturn($customerDataModel);
        $customerDataModel->expects($this->any())->method('getStoreId')->willReturn(1);
        $customerDataModel->expects($this->any())->method('getId')->willReturn(1);
        $this->sendEmailCheck();
        $this->resource->expects($this->atLeastOnce())->method('save')->willReturnSelf();

        $this->assertEquals(2, $this->design->subscribe($email));
    }

    public function testUpdateSubscription()
    {
        $customerId = 1;
        $customerDataMock = $this->getMockBuilder('\Magento\Customer\Api\Data\CustomerInterface')
            ->getMock();
        $this->customerRepository->expects($this->atLeastOnce())
            ->method('getById')
            ->with($customerId)->willReturn($customerDataMock);
        $this->resource->expects($this->atLeastOnce())
            ->method('loadByCustomerData')
            ->with($customerDataMock)
            ->willReturn(
                [
                    'design_id' => 1,
                    'design_status' => 1
                ]
            );
        $customerDataMock->expects($this->atLeastOnce())->method('getId')->willReturn('id');
        $this->resource->expects($this->atLeastOnce())->method('save')->willReturnSelf();
        $this->customerAccountManagement->expects($this->once())
            ->method('getConfirmationStatus')
            ->with($customerId)
            ->willReturn('account_confirmation_required');
        $customerDataMock->expects($this->once())->method('getStoreId')->willReturn('store_id');
        $customerDataMock->expects($this->once())->method('getEmail')->willReturn('email');

        $this->assertEquals($this->design, $this->design->updateSubscription($customerId));
    }

    public function testUnsubscribeCustomerById()
    {
        $customerId = 1;
        $customerDataMock = $this->getMockBuilder('\Magento\Customer\Api\Data\CustomerInterface')
            ->getMock();
        $this->customerRepository->expects($this->atLeastOnce())
            ->method('getById')
            ->with($customerId)->willReturn($customerDataMock);
        $this->resource->expects($this->atLeastOnce())
            ->method('loadByCustomerData')
            ->with($customerDataMock)
            ->willReturn(
                [
                    'design_id' => 1,
                    'design_status' => 1
                ]
            );
        $customerDataMock->expects($this->atLeastOnce())->method('getId')->willReturn('id');
        $this->resource->expects($this->atLeastOnce())->method('save')->willReturnSelf();
        $customerDataMock->expects($this->once())->method('getStoreId')->willReturn('store_id');
        $customerDataMock->expects($this->once())->method('getEmail')->willReturn('email');
        $this->sendEmailCheck();

        $this->design->unsubscribeCustomerById($customerId);
    }

    public function testSubscribeCustomerById()
    {
        $customerId = 1;
        $customerDataMock = $this->getMockBuilder('\Magento\Customer\Api\Data\CustomerInterface')
            ->getMock();
        $this->customerRepository->expects($this->atLeastOnce())
            ->method('getById')
            ->with($customerId)->willReturn($customerDataMock);
        $this->resource->expects($this->atLeastOnce())
            ->method('loadByCustomerData')
            ->with($customerDataMock)
            ->willReturn(
                [
                    'design_id' => 1,
                    'design_status' => 3
                ]
            );
        $customerDataMock->expects($this->atLeastOnce())->method('getId')->willReturn('id');
        $this->resource->expects($this->atLeastOnce())->method('save')->willReturnSelf();
        $customerDataMock->expects($this->once())->method('getStoreId')->willReturn('store_id');
        $customerDataMock->expects($this->once())->method('getEmail')->willReturn('email');
        $this->sendEmailCheck();

        $this->design->subscribeCustomerById($customerId);
    }

    public function testUnsubscribe()
    {
        $this->resource->expects($this->once())->method('save')->willReturnSelf();
        $this->sendEmailCheck();

        $this->assertEquals($this->design, $this->design->unsubscribe());
    }

    /**
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage This is an invalid subscription confirmation code.
     */
    public function testUnsubscribeException()
    {
        $this->design->setCode(111);
        $this->design->setCheckCode(222);

        $this->design->unsubscribe();
    }

    public function testGetDesignFullName()
    {
        $this->design->setFirstname('John');
        $this->design->setLastname('Doe');

        $this->assertEquals('John Doe', $this->design->getDesignFullName());
    }

    public function testConfirm()
    {
        $code = 111;
        $this->design->setCode($code);
        $this->resource->expects($this->once())->method('save')->willReturnSelf();

        $this->assertTrue($this->design->confirm($code));
    }

    public function testConfirmWrongCode()
    {
        $code = 111;
        $this->design->setCode(222);

        $this->assertFalse($this->design->confirm($code));
    }

    public function testReceived()
    {
        $queue = $this->getMockBuilder('\Designnbuy\Customer\Model\Queue')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource->expects($this->once())->method('received')->with($this->design, $queue)->willReturnSelf();

        $this->assertEquals($this->design, $this->design->received($queue));
    }

    protected function sendEmailCheck()
    {
        $storeModel = $this->getMockBuilder('\Magento\Store\Model\Store')
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMock();
        $transport = $this->getMock('\Magento\Framework\Mail\TransportInterface');
        $this->scopeConfig->expects($this->any())->method('getValue')->willReturn(true);
        $this->transportBuilder->expects($this->once())->method('setTemplateIdentifier')->willReturnSelf();
        $this->transportBuilder->expects($this->once())->method('setTemplateOptions')->willReturnSelf();
        $this->transportBuilder->expects($this->once())->method('setTemplateVars')->willReturnSelf();
        $this->transportBuilder->expects($this->once())->method('setFrom')->willReturnSelf();
        $this->transportBuilder->expects($this->once())->method('addTo')->willReturnSelf();
        $this->storeManager->expects($this->any())->method('getStore')->willReturn($storeModel);
        $storeModel->expects($this->any())->method('getId')->willReturn(1);
        $this->transportBuilder->expects($this->once())->method('getTransport')->willReturn($transport);
        $transport->expects($this->once())->method('sendMessage')->willReturnSelf();
        $this->inlineTranslation->expects($this->once())->method('suspend')->willReturnSelf();
        $this->inlineTranslation->expects($this->once())->method('resume')->willReturnSelf();

        return $this;
    }
}
