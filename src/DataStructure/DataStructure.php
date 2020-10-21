<?php

namespace AV\Form\DataStructure;

class DataStructure
{
    /** @var Field[] */
    protected array $fields = [];

    /** @var DataStructure[] */
    private array $nested = [];

    /** @var string[] */
    private array $parentNames = [];

    public function field(string $type, string $name): Field
    {
        $field = new Field($type, $name);
        $this->addField($field);

        return $field;
    }

    public function addField(Field $field): void
    {
        $this->fields[$field->getId()] = $field;
    }

    public function hasField(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    public function getField(string $name): Field
    {
        return $this->fields[$name];
    }

    /**
     * @return array|Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function string(string $name): Field
    {
        return $this->field('string', $name);
    }

    public function boolean(string $name): Field
    {
        return $this->field('boolean', $name);
    }

    public function integer(string $name): Field
    {
        return $this->field('integer', $name);
    }

    public function array(string $name): Field
    {
        return $this->field('array', $name);
    }

    public function nest(string $name, DataStructure $dataStructure): self
    {
        $this->nested[$name] = $dataStructure;

        $dataStructure->setParentNames(array_merge($this->parentNames, [$name]));

        return $this;
    }

    public function nested(string $name, callable $closure): self
    {
        $nestedStructure = new DataStructure();

        $closure($nestedStructure);

        $this->nest($name, $nestedStructure);

        return $this;
    }

    private function setParentNames(array $parentNames): void
    {
        $this->parentNames = $parentNames;

        foreach ($this->nested as $name => $nestedStructure) {
            $nestedStructure->setParentNames(array_merge($this->parentNames, [$name]));
        }
    }

    public function getParentNames(): array
    {
        return $this->parentNames;
    }

    /**
     * @return DataStructure[]
     */
    public function getAllNested(): array
    {
        return $this->nested;
    }

    public function hasNested(string $name): bool
    {
        return isset($this->nested[$name]);
    }

    public function getNested(string $name): DataStructure
    {
        return $this->nested[$name];
    }

    /**
     * @param array $fields
     */
    public function only(array $fields): void
    {
        $this->fields = array_intersect_key($this->fields, array_flip($fields));
    }

    public function exclude(array $fields): void
    {
        $this->fields = array_diff_key($this->fields, array_flip($fields));
    }
}
