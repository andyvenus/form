<?php

namespace AV\Form\Validator;

use InvalidArgumentException;

class ValidationResult
{
    private bool $isValid;

    /** @var array|ValidationError[] */
    private array $errors;

    private array $data;

    public function __construct(bool $isValid, array $errors = [], array $data = [])
    {
        $this->isValid = $isValid;
        $this->errors = $errors;
        $this->data = $data;

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
    public function getValue($key)
    {
        return $this->data[$key];
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
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

        $this->data[$nestName] = $validationResult->getData();
    }
}
