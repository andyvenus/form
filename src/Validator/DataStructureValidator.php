<?php

namespace AV\Form\Validator;

use AV\Form\DataStructure\DataStructure;

class DataStructureValidator
{
    private bool $trimData;

    /** @var array|ValidatorInterface[] */
    private array $validators = [];

    public function __construct(bool $trimData = true)
    {
        $this->trimData = $trimData;
    }

    public function addValidator(ValidatorInterface $validator)
    {
        $this->validators[] = $validator;
    }

    public function check(DataStructure $dataStructure, array $data): ValidationResult
    {
        $data = $this->trimArray($data);

        $fields = $dataStructure->getFields();

        $errors = [];
        $validData = [];
        $invalidData = [];
        $nestedDataStructures = [];

        foreach ($fields as $fieldId => $field) {
            $fieldHasError = false;

            if (!array_key_exists($fieldId, $data)) {
                // No value set
                if (!$field->hasDefault()) {
                    // No default
                    $errors[] = new ValidationError('No value was submitted for %label%', $field);
                    continue;
                }

                // No value set, but have default
                $value = $field->getDefault();
            } else {
                // Have a value
                $value = $data[$fieldId];
            }

            // Check if submitted value cannot be cast to the right type
            if (!$field->canCast($value)) {
                $errors[] = new ValidationError('The value submitted for %label% was in an invalid format', $field);
                $invalidData[$fieldId] = $value;
                continue;
            }

            $value = $field->cast($value);

            // Validate choice, unless field is nullable and value is null
            if ($field->hasChoices() && !$field->isChoice($value) && !($field->isNullable() && is_null($value))) {
                $errors[] = new ValidationError('The value submitted for %label% is not an available choice', $field);
                $invalidData[$fieldId] = $value;
                continue;
            }

            // Check the field using any assigned validators
            foreach ($this->validators as $validator) {
                $fieldResult = $validator->validateField($field, $value);

                if ($fieldResult->isInvalid()) {
                    $fieldHasError = true;
                    $errors = array_merge($errors, $fieldResult->getErrors());
                }
            }

            if ($fieldHasError) {
                $invalidData[$fieldId] = $value;
            } else {
                // Transform the data
                $value = $field->doTransform($value);

                $validData[$fieldId] = $value;
            }

            if ($field->hasDataStructure()) {
                $nestedDataStructures[$field->getName()] = $field->getDataStructure();
            }
        }

        $result = new ValidationResult(empty($errors), $errors, $validData, $invalidData);

        foreach ($nestedDataStructures as $nestedName => $nestedDataStructure) {
            $result->combineResult(
                $nestedDataStructure->getParentNames(),
                $this->check($nestedDataStructure, $data[$nestedName] ?? [])
            );
        }

        return $result;
    }

    private function trimArray(array $data)
    {
        if ($this->trimData) {
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = trim($value);
                }
                if (is_array($value)) {
                    $data[$key] = $this->trimArray($value);
                }
            }
        }

        return $data;
    }
}
