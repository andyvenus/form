<?php

namespace AV\Form\Tests\DataStructure;

use AV\Form\DataStructure\DataStructure;
use PHPUnit\Framework\TestCase;

class DataStructureTest extends TestCase
{
    public function testString()
    {
        $dataStructure = new DataStructure();
        $dataStructure->string('test');

        $this->assertTrue($dataStructure->getField('test')->checkType('test'));
    }

    public function testInteger()
    {
        $dataStructure = new DataStructure();
        $dataStructure->integer('test');

        $this->assertTrue($dataStructure->getField('test')->checkType(1));
    }

    public function testBoolean()
    {
        $dataStructure = new DataStructure();
        $dataStructure->boolean('test');

        $this->assertTrue($dataStructure->getField('test')->checkType(true));
    }

    public function testArray()
    {
        $dataStructure = new DataStructure();
        $dataStructure->array('test');

        $this->assertTrue($dataStructure->getField('test')->checkType([]));
    }
}
