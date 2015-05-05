<?php

namespace Stp\SndApi\News\Test\Validator;

use Stp\SndApi\News\Validator\ContentTypeValidator;

class ContentTypeValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function contentTypeProvider()
    {
        return [
            ['article', true],
            ['person', true],
            ['invalid', false]
        ];
    }

    /**
     * @dataProvider contentTypeProvider
     */
    public function testValidator($contentType, $expected)
    {
        $validator = new ContentTypeValidator($contentType);
        $this->assertEquals($expected, $validator->isValid());
    }
}
