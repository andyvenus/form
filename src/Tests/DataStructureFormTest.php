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

        $field = new Field('integer', 'test');
        $field->metadata('input_type', 'hidden');
        $data['manually specified field type'] = [$field, 'hidden'];

        return $data;
    }
}
