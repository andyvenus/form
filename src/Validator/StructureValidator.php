<?php

namespace AV\Form\Validator;

use AV\Form\DataStructure\DataStructure;

class StructureValidator
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
        $finalData = [];

        foreach ($fields as $fieldId => $field) {
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
                $finalData[$fieldId] = $value;
                continue;
            }

            $value = $field->cast($value);

            // Validate choice, unless field is nullable and value is null
            if ($field->hasChoices() && !$field->isChoice($value) && !($field->isNullable() && is_null($value))) {
                $errors[] = new ValidationError('The value submitted for %label% is not an available choice', $field);
                $finalData[$fieldId] = $value;
                continue;
            }

            // Check the field using any assigned validators
            foreach ($this->validators as $validator) {
                $fieldResult = $validator->validateField($field, $value);

                if ($fieldResult->isInvalid()) {
                    $errors = array_merge($errors, $fieldResult->getErrors());
                }
            }

            // Record the data, whether it is valid or not
            $finalData[$fieldId] = $value;
        }

        return new ValidationResult(empty($errors), $errors, $finalData);
    }

    private function trimArray(array $data)
    {
        if ($this->trimData) {
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = trim($value);
                }
            }
        }

        return $data;
    }
}