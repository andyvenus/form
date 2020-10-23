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

    public function testOnly()
    {
        $dataStructure = new DataStructure();
        $dataStructure->array('one');
        $dataStructure->array('two');

        $this->assertTrue($dataStructure->hasField('one'));
        $this->assertTrue($dataStructure->hasField('two'));

        $dataStructure->only(['one']);

        $this->assertTrue($dataStructure->hasField('one'));
        $this->assertFalse($dataStructure->hasField('two'));
    }

    public function testExclude()
    {
        $dataStructure = new DataStructure();
        $dataStructure->array('one');
        $dataStructure->array('two');

        $this->assertTrue($dataStructure->hasField('one'));
        $this->assertTrue($dataStructure->hasField('two'));

        $dataStructure->exclude(['two']);

        $this->assertTrue($dataStructure->hasField('one'));
        $this->assertFalse($dataStructure->hasField('two'));
    }

    public function testNested()
    {
        $dataStructure = new DataStructure();
        $dataStructure->nested('inner', function(DataStructure $inner) {
            $inner->string('test_string');
        });

        $this->assertTrue($dataStructure->getField('inner')->getDataStructure()->hasField('test_string'));
    }
}
