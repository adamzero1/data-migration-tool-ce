<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Migration\Handler\EavAttribute;

use Migration\Resource\Record;

/**
 * Class ConvertModelTest
 */
class ConvertConfigurableAttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ConvertModel
     */
    protected $handler;

    /**
     * @var string
     */
    protected $fieldName = 'field_to_handle';

    /**
     * @var \Migration\Reader\ClassMap|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $classMap;

    public function setUp()
    {
        $this->classMap = $this->getMockBuilder('Migration\Reader\ClassMap')->setMethods(['convertClassName'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = new ConvertConfigurableAttribute($this->classMap);
        $this->handler->setField($this->fieldName);
    }

    public function testHandleConfigurable()
    {
        $recordToHandle = $this->getMockBuilder('Migration\Resource\Record')
            ->setMethods(['getValue', 'setValue', 'getFields'])
            ->disableOriginalConstructor()
            ->getMock();
        $oppositeRecord = $this->getMockBuilder('Migration\Resource\Record')->disableOriginalConstructor()->getMock();
        $recordToHandle->expects($this->once())->method('getFields')->will($this->returnValue([$this->fieldName]));
        $recordToHandle->expects($this->any())->method('getValue')
            ->willReturnMap(
                [
                    [$this->fieldName, 'value'],
                    ['is_configurable', '1']
                ]
            );
        $recordToHandle->expects($this->once())->method('setValue')->with($this->fieldName, null);
        $this->handler->handle($recordToHandle, $oppositeRecord);
    }

    public function testHandleNotConfigurable()
    {
        $recordToHandle = $this->getMockBuilder('Migration\Resource\Record')
            ->setMethods(['getValue', 'setValue', 'getFields'])
            ->disableOriginalConstructor()
            ->getMock();
        $oppositeRecord = $this->getMockBuilder('Migration\Resource\Record')->disableOriginalConstructor()
            ->setMethods(['getValue'])
            ->getMock();
        $oppositeRecord->expects($this->exactly(2))->method('getValue')->will($this->returnValue('simple'));

        $recordToHandle->expects($this->once())->method('getFields')->will($this->returnValue([$this->fieldName]));
        $recordToHandle->expects($this->once())->method('getValue')->with($this->fieldName)
            ->will($this->returnValue(null));
        $recordToHandle->expects($this->once())->method('setValue')->with($this->fieldName, 'simple');

        $this->handler->handle($recordToHandle, $oppositeRecord);
    }
}
