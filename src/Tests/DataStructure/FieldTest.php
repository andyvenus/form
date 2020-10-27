<?php

namespace AV\Form\Tests\DataStructure;

use AV\Form\DataStructure\DataStructure;
use AV\Form\DataStructure\Field;
use AV\Form\Exception\InvalidTypeException;
use PHPUnit\Framework\TestCase;

class FieldTest extends TestCase
{
    /**
     * @param string $type
     * @dataProvider validTypesDataProvider
     */
    public function testInstantiateWithValidType(string $type)
    {
        new Field($type, 'test');

        // No exception means all is good
        $this->assertTrue(true);
    }

    public function validTypesDataProvider()
    {
        return [
            ['array'],
            ['integer'],
            ['float'],
            ['double'],
            ['string'],
            ['boolean'],
            ['null'],
        ];
    }

    public function testInstantiateWithInvalidType()
    {
        $this->expectException(InvalidTypeException::class);

        new Field('not-a-real-type', 'test');
    }

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

    public function testNestedDefault()
    {
        $dataStructure = new DataStructure();
        $dataStructure->string('inner')->default('abc');

        $field = new Field('array', 'test');
        $field->dataStructure($dataStructure);

        $this->assertSame($field->getDefault(), ['inner' => 'abc']);
    }

    public function testEmptyNestedDefault()
    {
        $dataStructure = new DataStructure();
        $dataStructure->string('inner');

        $field = new Field('array', 'test');
        $field->dataStructure($dataStructure);

        $this->assertSame($field->getDefault(), []);
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
            ['string', null, false],


            ['integer', 'test', false],
            ['integer', 1, true],
            ['integer', 1.11, true],
            ['integer', '1', true],
            ['integer', '1.11', true],
            ['integer', [], false],
            ['integer', new \stdClass(), false],
            ['integer', null, false],


            ['float', 'test', false],
            ['float', 1, true],
            ['float', 1.11, true],
            ['float', '1', true],
            ['float', '1.11', true],
            ['float', [], false],
            ['float', new \stdClass(), false],
            ['float', null, false],


            ['double', 'test', false],
            ['double', 1, true],
            ['double', 1.11, true],
            ['double', '1', true],
            ['double', '1.11', true],
            ['double', [], false],
            ['double', new \stdClass(), false],
            ['double', null, false],


            ['array', 'test', false],
            ['array', 1, false],
            ['array', 1.11, false],
            ['array', '1', false],
            ['array', '1.11', false],
            ['array', [], true],
            ['array', new \stdClass(), false],
            ['array', null, false],


            ['boolean', 'test', false],
            ['boolean', 1, true],
            ['boolean', 1.11, true],
            ['boolean', '1', true],
            ['boolean', '1.11', true],
            ['boolean', [], false],
            ['boolean', null, false],
        ];
    }

    public function testCanCastNullWhenNullable()
    {
        $field = new Field('string', 'test');
        $field->nullable();

        $this->assertTrue($field->canCast(null));
    }

    /**
     * @param string $type
     * @param $value
     * @dataProvider validCastDataProvider
     */
    public function testValidCast(string $type, $value)
    {
        $field = new Field($type, 'test');
        $field->nullable();

        $expectType = $type;
        if ($type === 'float') {
            $expectType = 'double';
        }

        $this->assertSame($expectType, strtolower(gettype($field->cast($value))));
    }

    public function validCastDataProvider()
    {
        return [
            ['null', null],

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

    /**
     * @param string $type
     * @param $value
     * @param bool $nullable
     * @throws InvalidTypeException
     * @dataProvider castDataProvider
     */
    public function testInvalidCast(string $type, $value)
    {
        $this->expectException(InvalidTypeException::class);

        $field = new Field($type, 'test');

        $field->cast($value);
    }

    public function castDataProvider()
    {
        return [
            ['string', []],
            ['string', null],

            ['integer', 'abc'],

            ['decimal', 'abc'],
        ];
    }

    public function testCastNullWhenNullable()
    {
        $field = new Field('string', 'test');
        $field->nullable();

        $this->assertNull($field->cast(null));
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
            'value' => 'Label'
        ]);

        $this->assertSame(['value' => 'Label'], $field->getChoiceLabels());
        $this->assertSame(['value'], $field->getChoices());
    }

    public function testTransform()
    {
        $field = new Field('string', 'test');

        $field->transform(fn($var) => str_replace('te', 'be', $var));

        $this->assertSame('best', $field->doTransform('test'));
    }
}
