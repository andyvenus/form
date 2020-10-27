<?php

namespace AV\Form;

use AV\Form\DataStructure\DataStructure;

class DataStructureForm extends FormBlueprint
{
    use DataStructureFormTrait;

    public function __construct(DataStructure $dataStructure)
    {
        $this->addFieldsFromDataStructure($dataStructure);
    }
}
