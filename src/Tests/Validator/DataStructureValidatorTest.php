<?php

namespace AV\Form\Tests\Validator;

use AV\Form\DataStructure\DataStructure;
use AV\Form\Validator\DataStructureValidator;
use AV\Form\Validator\ValidationError;
use AV\Form\Validator\ValidationResult;
use AV\Form\Validator\ValidatorInterface;
use Mockery;
use PHPUnit\Framework\TestCase;

class DataStructureValidatorTest extends TestCase
{
    public function testCheck()
    {
        $structure = new DataStructure();
        $structure->string('simple');

        $structureValidator = new DataStructureValidator();
        $result = $structureValidator->check($structure, ['simple' => 'value']);

        $this->assertTrue($result->isValid());
    }

    public function testCheckTrimsValues()
    {
        $structure = new DataStructure();
        $structure->string('simple');
        $structure->array('array');

        $structureValidator = new DataStructureValidator();
        $result = $structureValidator->check($structure, ['simple' => ' value ', 'array' => [' value ']]);

        $this->assertSame(5, strlen($result->getValidValue('simple')));
        $this->assertSame(5, strlen($result->getValidValue('array')[0]));
    }

    public function testCheckSplitValidAndInvalidData()
    {
        $structure = new DataStructure();
        $structure->string('one')->choices(['a']);
        $structure->string('two')->choices(['a']);

        $structureValidator = new DataStructureValidator();
        $result = $structureValidator->check($structure, ['one' => 'a', 'two' => 'b']);

        $this->assertSame(['one' => 'a'], $result->getValidData());
        $this->assertSame(['two' => 'b'], $result->getInvalidData());
    }

    public function testCheckFailsWithMissingValue()
    {
        $structure = new DataStructure();
        $structure->string('simple');

        $structureValidator = new DataStructureValidator();
        $result = $structureValidator->check($structure, []);

        $this->assertFalse($result->isValid());
        $this->assertStringContainsString('No value', $result->getErrors()[0]->getMessage());
    }

    public function testValueThatCannotBeCast()
    {
        $structure = new DataStructure();
        $structure->string('simple');

        $structureValidator = new DataStructureValidator();
        $result = $structureValidator->check($structure, ['simple' => []]);

        $this->assertFalse($result->isValid());
        $this->assertStringContainsString('invalid format', $result->getErrors()[0]->getMessage());
    }

    public function testCheckPassesWithMissingValueButThereIsADefault()
    {
        $structure = new DataStructure();
        $structure->string('simple')->default('abc');

        $structureValidator = new DataStructureValidator();
        $result = $structureValidator->check($structure, []);

        $this->assertTrue($result->isValid());
        $this->assertStringContainsString('abc', $result->getValidValue('simple'));
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

        $structureValidator = new DataStructureValidator();
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

        $structureValidator = new DataStructureValidator();

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

    /**
     * @param array $data
     * @param bool $expectValid
     * @dataProvider validateNestedDataStructureDataProvider
     */
    public function testValidateNestedDataStructureIsValid(array $data, bool $expectValid)
    {
        $dataStructure = new DataStructure();
        $dataStructure->nested('inner', function(DataStructure $inner) {
            $inner->string('abc')
                ->choices(['a', 'b']);
        });

        $validator = new DataStructureValidator();
        $result = $validator->check($dataStructure, $data);

        $this->assertSame($expectValid, $result->isValid());
    }

    public function validateNestedDataStructureDataProvider()
    {
        return [
            'valid choice' => [['inner' => ['abc' => 'a']], true],
            'invalid choice' => [['inner' => ['abc' => 'c']], false],
            'no data' => [[], false],
        ];
    }

    public function testValidateNestedDataStructureErrorsHaveParentNamesSet()
    {
        $dataStructure = new DataStructure();
        $dataStructure->nested('inner', function(DataStructure $inner) {
            $inner->string('abc')->choices(['a', 'b']);
        });

        $validator = new DataStructureValidator();
        $result = $validator->check($dataStructure, []);

        $this->assertFalse($result->isValid());

        $errors = $result->getErrors();
        $this->assertSame(['inner'], $errors[0]->getParentNames());
    }

    public function testDataIsTransformed()
    {
        $dataStructure = new DataStructure();
        $dataStructure->string('abc')
            ->transform(fn($data) => 'replaced');

        $validator = new DataStructureValidator();
        $result = $validator->check($dataStructure, ['abc' => 'original']);

        $this->assertTrue($result->isValid());

        $this->assertSame('replaced', $result->getValidValue('abc'));
    }
}
