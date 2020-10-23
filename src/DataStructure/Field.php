<?php

namespace AV\Form\DataStructure;

use AV\Form\Exception\InvalidTypeException;
use AV\Form\Validator\ValidationRuleInterface;

class Field
{
    private string $id;

    private string $type;

    private ?string $label = null;

    private $default;

    private bool $nullable = false;

    private ?array $choices = null;

    private ?array $choiceLabels = null;

    private array $metadata;

    private array $validationRules;

    private DataStructure $dataStructure;

    public function __construct(string $type, string $id)
    {
        $this->type = $type;
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function nullable($nullable = true)
    {
        $this->nullable = $nullable;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function default($value): self
    {
        $this->doTypeCheck($value);

        $this->default = $value;

        return $this;
    }

    public function hasDefault(): bool
    {
        return isset($this->default) || isset($this->dataStructure);
    }

    public function getDefault()
    {
        if (isset($this->default)) {
            return $this->default;
        }

        // Get the defaults for the nested data structure
        if (isset($this->dataStructure)) {
            $default = [];

            foreach ($this->dataStructure->getFields() as $field) {
                if ($field->hasDefault()) {
                    $default[$field->getId()] = $field->getDefault();
                }
            }

            return $default;
        }

        throw new \Exception('Tried to get the default value for a field that does not have one');
    }

    public function choices(array $choices): self
    {
        $this->choices = $choices;

        return $this;
    }

    public function hasChoices(): bool
    {
        return is_array($this->choices);
    }

    public function getChoices(): ?array
    {
        return $this->choices;
    }

    public function isChoice($value)
    {
        if (!$this->hasChoices()) {
            return false;
        }

        return in_array($value, $this->choices, true);
    }

    public function choiceLabels(array $choiceLabels): self
    {
        $this->choiceLabels = $choiceLabels;

        return $this;
    }

    public function getChoiceLabels(): ?array
    {
        return $this->choiceLabels;
    }

    public function labelledChoices(array $choices): self
    {
        $this->choiceLabels(array_flip($choices));
        $this->choices(array_values($choices));

        return $this;
    }

    public function metadata(string $key, $value): self
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    public function getMetadata(string $key)
    {
        return $this->metadata[$key] ?? null;
    }

    public function validationRule(ValidationRuleInterface $rule): self
    {
        $this->validationRules[] = $rule;

        return $this;
    }

    /**
     * @return ValidationRuleInterface[]
     */
    public function getValidationRules(): array
    {
        return $this->validationRules;
    }

    protected function doTypeCheck($value): void
    {
        if (is_null($value) && !$this->isNullable()) {
            throw new \TypeError("Field {$this->id} can not be null");
        }

        if (!$this->checkType($value)) {
            $type = gettype($value);

            throw new \TypeError("Field {$this->id} expected a '{$this->type}' default value, got '{$type}'");
        }
    }

    public function canCast($value): bool
    {
        if (is_null($value)) {
            return $this->isNullable();
        }

        if (is_object($value)) {
            return false;
        }

        switch ($this->type) {
            case 'array':
                return is_array($value);
            case 'integer':
            case 'float':
            case 'double':
                return is_numeric($value);
            case 'string':
                return !is_array($value);
            case 'boolean':
                return $this->checkType($value) || is_numeric($value);
            default:
                return false;
        }
    }

    public function cast($value)
    {
        $valueType = gettype($value);

        if ($this->checkType($value)) {
            return $value;
        }

        if (!$this->canCast($value)) {
            throw new InvalidTypeException("Cannot cast value of type {$valueType} to {$this->type}");
        }

        if ($this->isNullable() && is_null($value)) {
            return $value;
        }

        switch ($this->type) {
            case 'array':
                return (array) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'double':
                return (double) $value;
            case 'string':
                return (string) $value;
            case 'boolean':
                return $this->checkType($value) || is_numeric($value);
            default:
                throw new InvalidTypeException("Encountered issue casting type {$valueType} to {$this->type}");
        }
    }

    public function checkType($value)
    {
        return gettype($value) === $this->type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function hasDataStructure(): bool
    {
        return isset($this->dataStructure);
    }

    public function getDataStructure(): DataStructure
    {
        return $this->dataStructure;
    }

    public function dataStructure(DataStructure $dataStructure): self
    {
        if ($this->type !== 'array') {
            throw new \Exception("Field '{$this->getId()}': Only array fields can have a data structure");
        }

        $this->dataStructure = $dataStructure;

        return $this;
    }
}
