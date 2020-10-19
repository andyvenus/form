<?php

namespace AV\Form;

use AV\Form\DataStructure\DataStructure;
use AV\Form\DataStructure\Field;

class DataStructureForm extends FormBlueprint
{
    public function __construct(DataStructure $dataStructure)
    {
        foreach ($dataStructure->getFields() as $field) {
            $type = $this->getInputType($field);

            $this->add($field->getId(), $type, [
                'label' => $field->getLabel()
            ]);
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
}
