<?php

namespace AV\Form;

use AV\Form\DataStructure\DataStructure;
use AV\Form\DataStructure\Field;

class DataStructureForm extends FormBlueprint
{
    public function __construct(DataStructure $dataStructure)
    {
        $this->addFieldsFromDataStructure($dataStructure);
    }

    protected function addFieldsFromDataStructure(DataStructure $dataStructure)
    {
        foreach ($dataStructure->getFields() as $field) {
            $type = $this->getInputType($field);

            if ($field->hasDataStructure()) {
                $this->addFieldsFromDataStructure($field->getDataStructure());
                continue;
            }

            $this->add(
                $this->getInputName($field, $dataStructure->getParentNames()),
                $type,
                $this->getInputOptions($field)
            );
        }
    }

    protected function getInputType(Field $field)
    {
        if ($inputType = $field->getMetadata('input_type')) {
            return $inputType;
        }

        if ($field->hasChoices()) {
            return 'select';
        }

        if (in_array($field->getType(), ['float', 'integer', 'double'])) {
            return 'number';
        }

        return 'text';
    }

    protected function getInputName(Field $field, array $parentNames)
    {
        if (empty($parentNames)) {
            $fullName = $field->getId();
        } else {
            $fullName = array_shift($parentNames);

            foreach ($parentNames as $parentName) {
                $fullName .= "[{$parentName}]";
            }

            $fullName .= "[{$field->getId()}]";
        }

        if ($field->getType() === 'array' && !$field->hasDataStructure()) {
            return $fullName.'[]';
        }

        return $fullName;
    }

    protected function getInputOptions(Field $field)
    {
        $options = [
            'label' => $field->getLabel()
        ];

        // Array fields with choices = multiple-select
        if ($field->hasType('array') && $field->hasChoices()) {
            $options['attr']['multiple'] = true;
        }

        if ($field->hasChoices()) {
            $options['choices'] = $field->getLabelledChoices();
        }

        return $options;
    }
}
