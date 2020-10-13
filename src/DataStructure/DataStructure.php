<?php

namespace AV\Form\DataStructure;

class DataStructure
{
    /** @var array|Field[] */
    protected array $fields = [];

    public function field(string $type, string $name): Field
    {
        return $this->fields[$name] = new Field($type, $name);
    }

    public function hasField(string $name): bool
    {
        return isset($this->fields[$name]);
    }

    public function getField(string $name): Field
    {
        return $this->fields[$name];
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

    /**
     * @return array|Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }
}
