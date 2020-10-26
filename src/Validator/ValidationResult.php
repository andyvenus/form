<?php

namespace AV\Form\Validator;

use InvalidArgumentException;

class ValidationResult
{
    private bool $isValid;

    /** @var array|ValidationError[] */
    private array $errors;

    private array $validData;

    private array $invalidData;

    public function __construct(bool $isValid, array $errors = [], array $validData = [], array $invalidData = [])
    {
        $this->isValid = $isValid;
        $this->errors = $errors;
        $this->validData = $validData;
        $this->invalidData = $invalidData;

        if (!$isValid && empty($errors)) {
            throw new InvalidArgumentException('An invalid validation result was created without any errors');
        }
    }

    public function isValid(): bool
    {
        return $this->isValid;
    }

    public function isInvalid(): bool
    {
        return !$this->isValid;
    }

    /**
     * @return array|ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return mixed
     */
    public function getValidValue($key)
    {
        return $this->validData[$key];
    }

    public function getData(): array
    {
        return array_merge($this->validData, $this->invalidData);
    }

    public function getValidData(): array
    {
        return $this->validData;
    }

    public function getInvalidData(): array
    {
        return $this->invalidData;
    }

    public function combineResult(array $parentNames, ValidationResult $validationResult)
    {
        // If the nested result had validation errors, this result must be invalid
        if (!$validationResult->isValid) {
            $this->isValid = false;
        }

        foreach ($validationResult->getErrors() as $error) {
            $error->setParentNames($parentNames);
        }

        $this->errors = array_merge($this->errors, $validationResult->getErrors());

        $nestName = $parentNames[array_key_last($parentNames)];

        $this->validData[$nestName] = $validationResult->getValidData();
        $this->invalidData[$nestName] = $validationResult->getInvalidData();
    }
}
