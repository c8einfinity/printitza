<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Test\Unit\Model\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;

class CustomerPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Designnbuy\Customer\Model\Plugin\CustomerPlugin
     */
    protected $plugin;

    /**
     * @var \Designnbuy\Customer\Model\DesignFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $designFactory;

    /**
     * @var \Designnbuy\Customer\Model\Design|\PHPUnit_Framework_MockObject_MockObject
     */
    private $design;

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->designFactory = $this->getMockBuilder('\Designnbuy\Customer\Model\DesignFactory')
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->design = $this->getMockBuilder('\Designnbuy\Customer\Model\Design')
            ->setMethods(['loadByEmail', 'getId', 'delete', 'updateSubscription', 'subscribeCustomerById', 'unsubscribeCustomerById'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->designFactory->expects($this->any())->method('create')->willReturn($this->design);

        $this->objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);

        $this->plugin = $this->objectManager->getObject(
            'Designnbuy\Customer\Model\Plugin\CustomerPlugin',
            [
                'designFactory' => $this->designFactory
            ]
        );
    }

    public function testAfterSave()
    {
        $customerId = 1;
        $subject = $this->getMock('\Magento\Customer\Api\CustomerRepositoryInterface');
        $customer = $this->getMock('Magento\Customer\Api\Data\CustomerInterface');
        $customer->expects($this->once())->method('getId')->willReturn($customerId);
        $this->design->expects($this->once())->method('updateSubscription')->with($customerId)->willReturnSelf();

        $this->assertEquals($customer, $this->plugin->afterSave($subject, $customer));
    }

    public function testAroundSaveWithoutIsSubscribed()
    {
        $passwordHash = null;
        $customerId = 1;
        /** @var CustomerInterface | \PHPUnit_Framework_MockObject_MockObject $customer */
        $customer = $this->getMock('Magento\Customer\Api\Data\CustomerInterface');
        $proceed  = function(CustomerInterface $customer, $passwordHash = null) use($customer) {
            return $customer;
        };
        /** @var CustomerRepository | \PHPUnit_Framework_MockObject_MockObject $subject */
        $subject = $this->getMock('\Magento\Customer\Api\CustomerRepositoryInterface');

        $customer->expects($this->atLeastOnce())
            ->method("getId")
            ->willReturn($customerId);

        $this->assertEquals($customer, $this->plugin->aroundSave($subject, $proceed, $customer, $passwordHash));
    }

    /**
     * @return array
     */
    public function provideExtensionAttributeDataForAroundSave() {
        return [
            [true, true] ,
            [false, false]
        ];
    }

    /**
     * @dataProvider provideExtensionAttributeDataForAroundSave
     */
    public function testAroundSaveWithIsSubscribed($isSubscribed, $subscribeIsCreated) {
        $passwordHash = null;
        $customerId = 1;
        /** @var CustomerInterface | \PHPUnit_Framework_MockObject_MockObject $customer */
        $customer = $this->getMock('Magento\Customer\Api\Data\CustomerInterface');
        $extensionAttributes = $this
            ->getMockBuilder("Magento\Customer\Api\Data\CustomerExtensionInterface")
            ->setMethods(["getIsSubscribed", "setIsSubscribed"])
            ->getMock();

        $extensionAttributes
            ->expects($this->atLeastOnce())
            ->method("getIsSubscribed")
            ->willReturn($isSubscribed);

        $customer->expects($this->atLeastOnce())
            ->method("getExtensionAttributes")
            ->willReturn($extensionAttributes);

        if ($subscribeIsCreated) {
            $this->design->expects($this->once())
                ->method("subscribeCustomerById")
                ->with($customerId);
        } else {
            $this->design->expects($this->once())
                ->method("unsubscribeCustomerById")
                ->with($customerId);
        }

        $proceed  = function(CustomerInterface $customer, $passwordHash = null) use($customer) {
            return $customer;
        };
        /** @var CustomerRepository | \PHPUnit_Framework_MockObject_MockObject $subject */
        $subject = $this->getMock('\Magento\Customer\Api\CustomerRepositoryInterface');

        $customer->expects($this->atLeastOnce())
            ->method("getId")
            ->willReturn($customerId);

        $this->assertEquals($customer, $this->plugin->aroundSave($subject, $proceed, $customer, $passwordHash));
    }

    public function testAroundDelete()
    {
        $deleteCustomer = function () {
            return true;
        };
        $subject = $this->getMock('\Magento\Customer\Api\CustomerRepositoryInterface');
        $customer = $this->getMock('Magento\Customer\Api\Data\CustomerInterface');
        $customer->expects($this->once())->method('getEmail')->willReturn('test@test.com');
        $this->design->expects($this->once())->method('loadByEmail')->with('test@test.com')->willReturnSelf();
        $this->design->expects($this->once())->method('getId')->willReturn(1);
        $this->design->expects($this->once())->method('delete')->willReturnSelf();

        $this->assertEquals(true, $this->plugin->aroundDelete($subject, $deleteCustomer, $customer));
    }

    public function testAroundDeleteById()
    {
        $customerId = 1;
        $deleteCustomerById = function () {
            return true;
        };
        $subject = $this->getMock('\Magento\Customer\Api\CustomerRepositoryInterface');
        $customer = $this->getMock('Magento\Customer\Api\Data\CustomerInterface');
        $subject->expects($this->once())->method('getById')->willReturn($customer);
        $customer->expects($this->once())->method('getEmail')->willReturn('test@test.com');
        $this->design->expects($this->once())->method('loadByEmail')->with('test@test.com')->willReturnSelf();
        $this->design->expects($this->once())->method('getId')->willReturn(1);
        $this->design->expects($this->once())->method('delete')->willReturnSelf();

        $this->assertEquals(true, $this->plugin->aroundDeleteById($subject, $deleteCustomerById, $customerId));
    }
}
