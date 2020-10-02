<?php
/**
 * User: Andy
 * Date: 30/11/14
 * Time: 12:01
 */

namespace AV\Form\Tests\Type;

use AV\Form\Type\FileType;
use PHPUnit\Framework\TestCase;

class FileTypeTest extends TestCase
{
    /**
     * @var FileType
     */
    private $fileType;

    public function setUp(): void
    {
        $this->fileType = new FileType();
    }

    public function testUnsetRequest()
    {
        $this->assertTrue($this->fileType->allowUnsetRequest([]));
        $this->assertNull($this->fileType->getUnsetRequestData([]));
    }

    public function testIsValidRequestData()
    {
        $this->assertTrue($this->fileType->isValidRequestData([], __FILE__));
        $this->assertTrue($this->fileType->isValidRequestData([], new \stdClass()));
        $this->assertFalse($this->fileType->isValidRequestData([], 'not-a-file.txt'));
    }
}
