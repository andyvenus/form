<?php

namespace AV\Form\Validator;

use AV\Form\DataStructure\Field;

interface ValidationRuleInterface
{
    public function setField(Field $field): void;

    public function supportsValidator(ValidatorInterface $validator): bool;
}
