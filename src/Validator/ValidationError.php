<?php

namespace AV\Form\Validator;

use AV\Form\DataStructure\Field;

/**
 * Class ValidationError
 * @package AV\Form\Validator
 * todo: parse message params, translation support
 */
class ValidationError
{
    private string $message;

    private ?Field $field;

    private array $parentNames;

    public function __construct(string $message, ?Field $field = null)
    {
        $this->message = $message;
        $this->field = $field;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getField(): ?string
    {
        return $this->field;
    }

    public function getFieldNameDotFormat()
    {
        return implode('.', array_merge($this->parentNames, [$this->field->getName()]));
    }

    public function getParentNames(): array
    {
        return $this->parentNames;
    }

    public function setParentNames(array $parentNames): void
    {
        $this->parentNames = $parentNames;
    }
}
