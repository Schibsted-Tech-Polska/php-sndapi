<?php

namespace Stp\SndApi\News\Test\Validator;

use Stp\SndApi\News\Validator\ArticlesListParametersValidator;

class ArticlesListParametersValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function parametersProvider()
    {
        return [
            [
                'desked',
                [
                    'areaLimit' => 1
                ],
                true
            ],
            [
                'desked',
                [
                    'invalid' => true
                ],
                false
            ],
            [
                'auto',
                [
                    'offset' => 20,
                    'limit' => 20
                ],
                true
            ],
            [
                'auto',
                [
                    'offset' => 20,
                    'limit' => 20,
                    'invalid' => true
                ],
                false
            ]
        ];
    }

    /**
     * @dataProvider parametersProvider
     */
    public function testValidator($method, $parameters, $expected)
    {
        $validator = new ArticlesListParametersValidator($method, $parameters);
        $this->assertEquals($expected, $validator->isValid());
    }
}
