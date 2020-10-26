<?php

namespace AV\Form\Tests;

use AV\Form\DataStructure\DataStructure;
use AV\Form\DataStructure\Field;
use AV\Form\DataStructureForm;
use PHPUnit\Framework\TestCase;

class DataStructureFormTest extends TestCase
{
    /**
     * @param Field $field
     * @param string $expectedFormFieldType
     * @dataProvider defaultInputTypeDataProvider
     */
    public function testDefaultInputType(Field $field, string $expectedFormFieldType)
    {
        $structure = new DataStructure();
        $structure->addField($field);

        $formBlueprint = new DataStructureForm($structure);

        $formField = $formBlueprint->get($field->getId());
        $this->assertSame($expectedFormFieldType, $formField['type']);
    }

    public function defaultInputTypeDataProvider()
    {
        $field = new Field('string', 'test');
        $field->choices(['a', 'b']);
        $data['field with choices'] = [$field, 'select'];

        $field = new Field('string', 'test');
        $data['simple string field'] = [$field, 'text'];

        $field = new Field('integer', 'test');
        $data['simple int field'] = [$field, 'number'];

        $field = new Field('array', 'test');
        $field->metadata('array', 'hidden');
        $data['array field'] = [$field, 'collection'];

        $field = new Field('integer', 'test');
        $field->metadata('input_type', 'hidden');
        $data['manually specified field type'] = [$field, 'hidden'];

        return $data;
    }

    public function testDefaultForArrayDataStructureField()
    {
        $innerDataStructure = new DataStructure();
        $innerDataStructure->string('abc');

        $structure = new DataStructure();
        $structure->nest('test', $innerDataStructure);

        $formBlueprint = new DataStructureForm($structure);

        $formField = $formBlueprint->get($structure->getField('test')->getId());
        $this->assertSame('collection', $formField['type']);

        $this->assertTrue(isset($formField['fields']['abc']));
        $this->assertSame('text', $formField['fields']['abc']['type']);
    }

    /**
     * @param array $choices
     * @param array $choiceLabels
     * @param array $expectFormChoices
     * @dataProvider choicesDataProvider
     */
    public function testChoices(array $choices, array $choiceLabels, array $expectFormChoices)
    {
        $structure = new DataStructure();
        $structure->array('test')
            ->choices($choices)
            ->choiceLabels($choiceLabels);

        $formBlueprint = new DataStructureForm($structure);

        $formField = $formBlueprint->get('test');

        $this->assertSame($expectFormChoices, $formField['options']['choices']);
    }

    public function choicesDataProvider()
    {
        return [
            'no labels' => [
                ['abc'],
                [],
                ['abc' => 'abc']
            ],
            'with labels' => [
                ['abc'],
                ['abc' => '123'],
                ['abc' => '123']
            ],
        ];
    }

    public function testArrayFieldWithChoicesHasMultipleOptionSet()
    {
        $structure = new DataStructure();
        $structure->array('test')
            ->choices(['a', 'b', 'c']);

        $formBlueprint = new DataStructureForm($structure);

        $formField = $formBlueprint->get($structure->getField('test')->getId());
        $this->assertSame('select', $formField['type']);
        $this->assertTrue($formField['options']['attr']['multiple']);
    }
}
