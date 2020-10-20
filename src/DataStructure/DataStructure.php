<?php

namespace AV\Form\DataStructure;

class DataStructure
{
    /** @var array|Field[] */
    protected array $fields = [];

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
