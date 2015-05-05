<?php

namespace Stp\SndApi\Common\Test\Validator;

use Stp\SndApi\Common\Validator\PublicationIdValidator;

class PublicationIdValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function publicationIdProvider()
    {
        return [
            ['common', true],
            ['sa', true],
            ['fvn', true],
            ['bt', true],
            ['ap', true],
            ['invalid', false]
        ];
    }

    /**
     * @dataProvider publicationIdProvider
     */
    public function testValidator($data, $expected)
    {
        $validator = new PublicationIdValidator($data);
        $this->assertEquals($expected, $validator->isValid());
    }
}
