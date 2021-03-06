<?php
/**
 * User: Andy
 * Date: 10/01/2014
 * Time: 13:57
 */

namespace AV\Form\Tests;

use AV\Form\FormBlueprint;

class FormBlueprintTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var FormBlueprint
     */
    private $form;

    public function setUp()
    {
        $this->form = new FormBlueprint();
    }

    public function testAddField()
    {
        $this->form->add('name', 'text', array(
            'label' => 'Name'
        ));

        $this->form->add('description', 'textarea');

        $this->assertTrue($this->form->has('name'));
        $this->assertTrue($this->form->has('description'));
    }

    public function testAddArrayField()
    {
        $this->form->add('names[]', 'select', ['accept_array' => true]);

        $this->assertNotNull($this->form->has('names'));
    }

    public function testDuplicateAdd()
    {
        $this->setExpectedException('\Exception', "Can't add field, field 'name' already exists");

        $this->form->add('name', 'text');
        $this->form->add('name', 'text');
    }

    public function testAddAfter()
    {
        $this->form->add('one', 'text');

        $this->form->add('three', 'text');

        $this->form->add('four', 'text');

        $this->form->addAfter('one', 'two', 'text');

        $this->form->addAfter('four', 'five', 'text');

        $fields = $this->form->getFieldNames();

        $expected = array('one', 'two', 'three', 'four', 'five');

        $this->assertEquals($expected, $fields);
    }

    public function testAddBefore()
    {
        $this->form->add('one', 'text');

        $this->form->add('three', 'text');

        $this->form->add('four', 'text');

        $this->form->addBefore('three', 'two', 'text');

        $fields = $this->form->getFieldNames();

        $expected = array('one', 'two', 'three', 'four');

        $this->assertEquals($expected, $fields);
    }

    public function testAddAferWhenNotExist()
    {
        $this->form->add('one', 'text');

        $this->form->addBefore('non-existant', 'two', 'text');

        $expected = array('one', 'two');
        $fields = $this->form->getFieldNames();

        $this->assertEquals($expected, $fields);
    }

    public function testRemoveElement()
    {
        $this->form->add('name', 'text');

        $this->assertTrue($this->form->has('name'));

        $this->form->remove('name');

        $this->assertFalse($this->form->has('name'));
    }

    public function testReplaceField()
    {
        $this->form->add('name', 'text', array('label' => 'Original Label'));

        $this->form->replace('name', 'textarea', array('label' => 'Replacement Label'));

        $field = $this->form->get('name');

        $this->assertEquals('textarea', $field['type']);
        $this->assertEquals('Replacement Label', $field['options']['label']);
    }

    public function testInvalidReplaceElement()
    {
        $this->setExpectedException('\Exception');

        $this->form->replace('does-not-exist', 'text');
    }

    public function testGetAll()
    {
        $this->form->add('name', 'text', array('label' => 'Name'));

        $this->form->add('category', 'select', array(
            'choices' => array(
                'category_one' => 'Category One',
                'category_two' => 'Category Two'
            ),
            'label' => 'Category'
        ));

        $this->form->add('url', 'textarea', array('label' => 'URL'));

        $expected_result = array(
            'name' => array(
                'name' => 'name',
                'type' => 'text',
                'options' => array(
                    'label' => 'Name'
                )
            ),
            'category' => array(
                'name' => 'category',
                'type' => 'select',
                'options' => array(
                    'choices' => array(
                        'category_one' => 'Category One',
                        'category_two' => 'Category Two'
                    ),
                    'label' => 'Category'
                )
            ),
            'url' => array(
                'name' => 'url',
                'type' => 'textarea',
                'options' => array(
                    'label' => 'URL'
                )
            )
        );

        $this->assertEquals($expected_result, $this->form->getAll());
    }

    public function testSetAction()
    {
        $this->assertEquals($this->form, $this->form->setAction('POST', 'http://www.example.com/page'));
    }

    public function testSetMethod()
    {
        $this->assertEquals($this->form, $this->form->setMethod('GET'));
    }

    public function testUnsetGet()
    {
        $this->assertNull($this->form->get('non-existant'));
    }

    public function testSections()
    {
        $this->form->addSection('my_section', 'My Section');

        $this->assertTrue($this->form->hasSection('my_section'));

        $this->form->removeSection('my_section');

        $this->assertFalse($this->form->hasSection('my_section'));
    }

    public function testSuccessMessage()
    {
        $this->form->setSuccessMessage('Done!!');

        $this->assertEquals('Done!!', $this->form->getSuccessMessage());
    }

    public function testCreateFieldsFromArray()
    {
        $fieldsArray = array(
            'field1' => array(),
            'field2' => array(
                'type' => 'select',
                'section' => 'a_section'
            )
        );

        $this->form->createFieldsFromArray($fieldsArray, null, 'main_section');

        $this->assertArrayHasKey('section', $this->form->get('field1')['options']);
        $this->assertEquals('main_section', $this->form->get('field1')['options']['section']);

        $this->assertEquals('a_section', $this->form->get('field2')['options']['section']);
    }

    public function testCreateFieldsFromArrayWithGroup()
    {
        $fieldsArray = array(
            'field1' => array()
        );

        $this->form->createFieldsFromArray($fieldsArray, 'test_group');

        $this->assertNull($this->form->get('field1'));
        $this->assertNotNull($this->form->get('test_group')['fields']['field1']);
    }

    public function testCreateSectionsFromArray()
    {
        $this->form->createSectionsFromArray(['1' => ['label' => 'One'], '2' => ['label' => 'Two']]);

        $this->assertTrue($this->form->hasSection('1'));
        $this->assertTrue($this->form->hasSection('2'));
        $this->assertFalse($this->form->hasSection('3'));
    }
}
 