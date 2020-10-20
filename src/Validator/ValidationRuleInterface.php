<?php

namespace AV\Form\Validator;

interface ValidationRuleInterface
{
    public function supportsValidator(ValidatorInterface $validator): bool;

    public function check(ValidatorInterface $validator): ValidationRuleResult;
}
