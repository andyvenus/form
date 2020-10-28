<?php

namespace AV\Form\Validator;

class FieldValidationResult
{
    /** @var ValidationError[] */
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    public function isValid(): bool
    {
        return empty($this->errors);
    }

    public function isInvalid(): bool
    {
        return !$this->isValid();
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addErrors(array $errors): void
    {
        $this->errors += $errors;
    }
}
