<?php

namespace AV\Form\Tests\DataStructure;

use AV\Form\DataStructure\Field;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    /**
     * @param string $type
     * @param $value
     * @dataProvider defaultDataProvider
     */
    public function testDefault(string $type, $value)
    {
        $field = new Field($type, 'test');

        $field->default($value);

        $this->assertSame($field->getDefault(), $value);
    }

    public function defaultDataProvider()
    {
        return [
            ['string', 'string'],
            ['integer', 1],
            ['boolean', true],
        ];
    }

    /**
     * @param string $type
     * @param $value
     * @dataProvider defaultWithWrongTypeDataProvider
     */
    public function testDefaultWithWrongType(string $type, $value)
    {
        $this->expectException(\TypeError::class);

        $field = new Field($type, 'test');

        $field->default($value);
    }

    public function defaultWithWrongTypeDataProvider()
    {
        return [
            ['string', 1],
            ['int', 'test'],
            ['bool', 1],
        ];
    }

    public function testLabel()
    {
        $field = new Field('string', 'test');

        $field->label('test label');

        $this->assertSame('test label', $field->getLabel());
    }

    /**
     * @param string $type
     * @param $value
     * @param bool $expected
     * @dataProvider canCastDataProvider
     */
    public function testCanCast(string $type, $value, bool $expected)
    {
        $field = new Field($type, 'test');

        $this->assertSame($expected, $field->canCast($value));
    }

    public function canCastDataProvider()
    {
        return [
            ['string', 'test', true],
            ['string', 1, true],
            ['string', 1.11, true],
            ['string', [], false],
            ['string', new \stdClass(), false],


            ['integer', 'test', false],
            ['integer', 1, true],
            ['integer', 1.11, true],
            ['integer', '1', true],
            ['integer', '1.11', true],
            ['integer', [], false],
            ['integer', new \stdClass(), false],


            ['float', 'test', false],
            ['float', 1, true],
            ['float', 1.11, true],
            ['float', '1', true],
            ['float', '1.11', true],
            ['float', [], false],
            ['float', new \stdClass(), false],


            ['double', 'test', false],
            ['double', 1, true],
            ['double', 1.11, true],
            ['double', '1', true],
            ['double', '1.11', true],
            ['double', [], false],
            ['double', new \stdClass(), false],


            ['array', 'test', false],
            ['array', 1, false],
            ['array', 1.11, false],
            ['array', '1', false],
            ['array', '1.11', false],
            ['array', [], true],
            ['array', new \stdClass(), false],


            ['boolean', 'test', false],
            ['boolean', 1, true],
            ['boolean', 1.11, true],
            ['boolean', '1', true],
            ['boolean', '1.11', true],
            ['boolean', [], false],
            ['boolean', new \stdClass(), false],
        ];
    }

    /**
     * @param string $type
     * @param $value
     * @dataProvider castDataProvider
     */
    public function testCast(string $type, $value)
    {
        $field = new Field($type, 'test');

        $expectType = $type;
        if ($type === 'float') {
            $expectType = 'double';
        }

        $this->assertSame($expectType, gettype($field->cast($value)));
    }

    public function castDataProvider()
    {
        return [
            ['string', 'test'],
            ['string', 1],
            ['string', 1.11],

            ['integer', 1],
            ['integer', 1.11],
            ['integer', '1'],
            ['integer', '1.11'],

            ['float', 1],
            ['float', 1.11],
            ['float', '1'],
            ['float', '1.11'],


            ['double', 1],
            ['double', 1.11],
            ['double', '1'],
            ['double', '1.11'],


            ['array', []],

            ['boolean', 1],
            ['boolean', 1.11],
            ['boolean', '1'],
            ['boolean', '1.11'],
        ];
    }

    public function testChoicesAndGetChoices()
    {
        $choices = ['abc', '123'];

        $field = new Field('string', 'test');
        $field->choices($choices);

        $this->assertSame($choices, $field->getChoices());
    }

    public function testChoicesLabelsAndGetChoiceLabels()
    {
        $choices = ['abc', '123'];
        $labels = ['abc' => 'Test'];

        $field = new Field('string', 'test');
        $field->choices($choices);
        $field->choiceLabels($labels);

        $this->assertSame($labels, $field->getChoiceLabels());
    }

    public function testLabelledChoices()
    {
        $field = new Field('string', 'test');
        $field->labelledChoices([
            'Label' => 'value'
        ]);

        $this->assertSame(['value' => 'Label'], $field->getChoiceLabels());
        $this->assertSame(['value'], $field->getChoices());
    }
}
