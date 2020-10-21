<?php

namespace AV\Form\Tests\Validator;

use AV\Form\DataStructure\Field;
use AV\Form\Validator\ValidationError;
use PHPUnit\Framework\TestCase;

class ValidationErrorTest extends TestCase
{
    public function testGetFieldNameDotFormat()
    {
        $validationError = new ValidationError('test', new Field('string', 'field'));
        $validationError->setParentNames(['outer', 'inner']);

        $this->assertSame('outer.inner.field', $validationError->getFieldNameDotFormat());
    }
}
