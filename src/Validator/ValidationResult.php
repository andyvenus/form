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
}
