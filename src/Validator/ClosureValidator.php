<?php

namespace AV\Form\Validator;

use AV\Form\DataStructure\Field;

class ClosureValidator implements ValidatorInterface
{
    public function validateField(Field $field, $value): FieldValidationResult
    {
        $errors = [];

        foreach ($field->getValidationRules() as $rule) {
            if (!$rule instanceof ClosureValidationRule) {
                continue;
            }

            $ruleResult = new FieldValidationResult();

            $closure = $rule->getClosure();

            $closure($value, $ruleResult, $field);

            foreach ($ruleResult->getErrors() as $error) {
                $error->setField($field);
                $errors[] = $field;
            }

            return $ruleResult;
        }

        return new FieldValidationResult($errors);
    }
}
