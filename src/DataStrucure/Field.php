<?php

namespace AV\Form\DataStrucure;

class Field
{
    private string $type;

    private string $label;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
