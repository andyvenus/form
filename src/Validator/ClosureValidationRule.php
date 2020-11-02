<?php

namespace AV\Form\Validator;

use AV\Form\DataStructure\Field;
use Closure;

class ClosureValidationRule implements ValidationRuleInterface
{
    private Field $field;

    private Closure $closure;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public function setField(Field $field): void
    {
        $this->field = $field;
    }

    public function supportsValidator(ValidatorInterface $validator): bool
    {
        return $validator instanceof ClosureValidator;
    }

    public function getClosure(): Closure
    {
        return $this->closure;
    }
}
