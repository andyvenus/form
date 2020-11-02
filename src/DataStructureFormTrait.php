<?php

namespace AV\Form;

use AV\Form\DataStructure\DataStructure;
use AV\Form\DataStructure\Field;

trait DataStructureFormTrait
{
    protected DataStructure $dataStructure;

    public function useDataStructure(DataStructure $dataStructure)
    {
        $this->dataStructure = $dataStructure;
    }

    protected function addFieldsFromDataStructure(DataStructure $dataStructure)
    {
        $this->useDataStructure($dataStructure);

        foreach ($dataStructure->getFields() as $field) {
            $this->addFor($field->getName());
        }
    }

    protected function addFor(string $fieldName, array $options = [])
    {
        if (!isset($this->dataStructure)) {
            throw new \Exception("You must set a DataStructure using 'useDataStructure' before using 'addFor'");
        }

        $field = $this->dataStructure->getField($fieldName);

        $type = $options['type'] ?? $this->getInputType($field);

        if ($field->hasDataStructure()) {
            $this->addFieldsFromDataStructure($field->getDataStructure());
            return;
        }

        $this->add(
            $this->getInputName($field, $this->dataStructure->getParentNames()),
            $type,
            $this->getInputOptions($field, $options)
        );
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

        if ($field->getType() === 'boolean') {
            return 'checkbox';
        }

        return 'text';
    }

    protected function getInputName(Field $field, array $parentNames)
    {
        if (empty($parentNames)) {
            $fullName = $field->getName();
        } else {
            $fullName = array_shift($parentNames);

            foreach ($parentNames as $parentName) {
                $fullName .= "[{$parentName}]";
            }

            $fullName .= "[{$field->getName()}]";
        }

        if ($field->getType() === 'array' && !$field->hasDataStructure()) {
            return $fullName.'[]';
        }

        return $fullName;
    }

    protected function getInputOptions(Field $field, array $mergeWith = [])
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
        
        $fieldOptions = [];
        if ($field->hasMetadata('input_options')) {
            $fieldOptions = $field->getMetadata('input_options');
        }

        return array_merge_recursive($options, $mergeWith, $fieldOptions);
    }
}
