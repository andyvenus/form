<?php

namespace AV\Form\DataStrucure;

class DataStructure
{
    public function field(string $type): Field
    {
        return new Field($type);
    }
}
