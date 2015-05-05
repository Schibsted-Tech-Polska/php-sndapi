<?php

namespace Stp\SndApi\News\Test\Validator;

use Stp\SndApi\News\Validator\ArticleListMethodValidator;

class ArticleListMethodValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function methodsProvider()
    {
        return [
            ['desked', true],
            ['auto', true],
            ['invalid', false]
        ];
    }

    /**
     * @dataProvider methodsProvider
     */
    public function testValidator($data, $expected)
    {
        $validator = new ArticleListMethodValidator($data);
        $this->assertEquals($expected, $validator->isValid());
    }
}
