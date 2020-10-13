<?php

namespace AV\Form\Tests\Validator;

use AV\Form\DataStructure\DataStructure;
use AV\Form\Validator\StructureValidator;
use AV\Form\Validator\ValidationError;
use AV\Form\Validator\ValidationResult;
use AV\Form\Validator\ValidatorInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class StructureValidatorTest extends TestCase
{
    public function testCheck()
    {
        $structure = new DataStructure();
        $structure->string('simple');

        $structureValidator = new StructureValidator();
        $result = $structureValidator->check($structure, ['simple' => 'value']);

        $this->assertTrue($result->isValid());
    }

    public function testCheckTrimsValues()
    {
        $structure = new DataStructure();
        $structure->string('simple');

        $structureValidator = new StructureValidator();
        $result = $structureValidator->check($structure, ['simple' => ' value ']);

        $this->assertSame(5, strlen($result->getValue('simple')));
    }

    public function testCheckFailsWithMissingValue()
    {
        $structure = new DataStructure();
        $structure->string('simple');

        $structureValidator = new StructureValidator();
        $result = $structureValidator->check($structure, []);

        $this->assertFalse($result->isValid());
        $this->assertStringContainsString('No value', $result->getErrors()[0]->getMessage());
    }

    public function testValueThatCannotBeCast()
    {
        $structure = new DataStructure();
        $structure->string('simple');

        $structureValidator = new StructureValidator();
        $result = $structureValidator->check($structure, ['simple' => []]);

        $this->assertFalse($result->isValid());
        $this->assertStringContainsString('invalid format', $result->getErrors()[0]->getMessage());
    }

    public function testCheckPassesWithMissingValueButThereIsADefault()
    {
        $structure = new DataStructure();
        $structure->string('simple')->default('abc');

        $structureValidator = new StructureValidator();
        $result = $structureValidator->check($structure, []);

        $this->assertTrue($result->isValid());
        $this->assertStringContainsString('abc', $result->getValue('simple'));
    }

    /**
     * @param string $type
     * @param array $choices
     * @param $value
     * @param bool $isNullable
     * @param bool $expectPass
     * @dataProvider checkValidatesChoiceDataProvider
     */
    public function testCheckValidatesChoice(
        string $type,
        array $choices,
        $value,
        bool $isNullable,
        bool $expectPass
    ) {
        $structure = new DataStructure();
        $structure->field($type, 'simple')
            ->choices($choices)
            ->nullable($isNullable);

        $structureValidator = new StructureValidator();
        $result = $structureValidator->check($structure, ['simple' => $value]);

        $this->assertSame($expectPass, $result->isValid());
    }

    public function checkValidatesChoiceDataProvider()
    {
        return [
            'valid_string' => ['string', ['abc', 'def'], 'abc', false, true],
            'invalid_string' => ['string', ['abc', 'def'], 'wrong', false, false],

            'not nullable null' => ['string', ['abc', 'def'], null, false, false],
            'nullable null' => ['string', ['abc', 'def'], null, true, true],
        ];
    }

    /**
     * @param bool $expectedValidationResult
     * @dataProvider booleanDataProvider
     */
    public function testSetValidatorIsUsed(bool $expectedValidationResult)
    {
        $structure = new DataStructure();
        $structure->string('simple');

        $structureValidator = new StructureValidator();

        $validationError = new ValidationError('Test Error');

        $validator = Mockery::mock(ValidatorInterface::class)
            ->expects('validateField')
            ->once()
            ->andReturn(new ValidationResult($expectedValidationResult, [
                $validationError
            ]))
            ->getMock();

        $structureValidator->addValidator($validator);

        $result = $structureValidator->check($structure, ['simple' => 'value']);

        $this->assertSame($expectedValidationResult, $result->isValid());

        if (!$expectedValidationResult) {
            $this->assertContains($validationError, $result->getErrors());
        }
    }

    public function booleanDataProvider()
    {
        return [
            [true],
            [false]
        ];
    }
}
