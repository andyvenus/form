<?php

namespace AV\Form\Validator;

use InvalidArgumentException;

class ValidationRuleResult
{
    private bool $isValid;

    /** @var array|ValidationError[] */
    private array $errors;

    public function __construct(bool $isValid, array $errors = [])
    {
        $this->isValid = $isValid;
        $this->errors = $errors;

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
}
