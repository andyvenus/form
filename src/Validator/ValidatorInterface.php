<?php


namespace AV\Form\Validator;


use AV\Form\DataStructure\Field;

interface ValidatorInterface
{
    public function validateField(Field $field, $value): FieldValidationResult;
}
