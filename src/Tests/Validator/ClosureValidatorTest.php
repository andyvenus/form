<?php

namespace AV\Form\Tests\Validator;

use AV\Form\DataStructure\Field;
use AV\Form\Validator\ClosureValidator;
use AV\Form\Validator\FieldValidationResult;
use PHPUnit\Framework\TestCase;

class ClosureValidatorTest extends TestCase
{
    /**
     * @param int $number
     * @param bool $expectValid
     * @dataProvider validateFieldDataProvider
     */
    public function testValidateField(int $number, bool $expectValid)
    {
        $field = new Field('integer', 'test');
        $field->validateWith(function($value, FieldValidationResult $result) {
            if ($value < 10) {
                $result->addErrorString('Number too small. It must be larger than 10.');
            }
        });

        $closureValidator = new ClosureValidator();
        $result = $closureValidator->validateField($field, $number);

        $this->assertSame($expectValid, $result->isValid());
    }

    public function validateFieldDataProvider()
    {
        return [
            [1, false],
            [15, true]
        ];
    }
}
