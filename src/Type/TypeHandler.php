<?php
/**
 * User: Andy
 * Date: 25/03/2014
 * Time: 14:15
 */

namespace AV\Form\Type;

use AV\Form\FormHandler;

class TypeHandler implements TypeInterface
{
    private $types;

    public function __construct($types = [])
    {
        $this->types = array_merge(array(
            'checkbox' => new CheckboxType(),
            'collection' => new CollectionType($this),
            'default' => new DefaultType(),
            'select' => new SelectType(),
            'file' => new FileType(),
            'radio' => new RadioType(),
            'text' => new TextType(),
            'textarea' => new TextareaType(),
        ), $types);
    }

    /**
     * @param $fieldType
     * @return TypeInterface
     */
    public function getType($fieldType)
    {
        if (isset($this->types[$fieldType])) {
            return $this->types[$fieldType];
        }
        else {
            return $this->types['default'];
        }
    }

    public function addType($id, TypeInterface $type)
    {
        $this->types[$id] = $type;
    }

    public function getDefaultOptions($field)
    {
        $type = $this->getType($field['type']);

        return $type->getDefaultOptions($field);
    }

    public function isValidRequestData($field, $data)
    {
        $type = $this->getType($field['type']);

        return $type->isValidRequestData($field, $data);
    }

    public function allowUnsetRequest($field)
    {
        $type = $this->getType($field['type']);

        return $type->allowUnsetRequest($field);
    }

    public function processRequestData($field, $data)
    {
        $type = $this->getType($field['type']);

        return $type->processRequestData($field, $data);
    }

    public function getUnsetRequestData($field)
    {
        $type = $this->getType($field['type']);

        return $type->getUnsetRequestData($field);
    }

    public function makeView($field, $allFormData, FormHandler $formHandler)
    {
        $type = $this->getType($field['type']);

        return $type->makeView($field, $allFormData, $formHandler);
    }
}
