<?php
namespace Unific\Connector\Test\Unit\Helper\Data;

class FormatterTest extends \PHPUnit\Framework\TestCase
{
    protected $_objectManager;
    protected $_helper;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->_helper = $this->_objectManager->getObject("Unific\Connector\Helper\Data\Formatter");
    }

    public function tearDown()
    {
    }

    /**
     * this function tests the result of the addition of two numbers
     *
     */
    public function testSetStreetData()
    {
        $testData = [];
        $originalAddress = '123 Second Street 12\nP.O. Box 31000\nSuite 3a';

        $result = $this->_helper->setStreetData($testData, $originalAddress);

        $this->assertEquals($result['street'], '123 Second Street 12');
        $this->assertEquals($result['street1'], 'P.O. Box 31000');
        $this->assertEquals($result['street2'], 'Suite 3a');
    }
}
