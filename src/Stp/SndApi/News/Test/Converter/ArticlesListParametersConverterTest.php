<?php

namespace Stp\SndApi\News\Test\Converter;

use Stp\SndApi\News\Converter\ArticlesListParametersConverter;

class ArticlesListParametersConverterTest extends \PHPUnit_Framework_TestCase
{
    public function parametersProvider()
    {
        return [
            [
                [
                    'includeSubsections' => true,
                    'homeSectionOnly' => 0
                ],
                [
                    'includeSubsections' => 'true',
                    'homeSectionOnly' => 'false'
                ],
                true
            ],
            [
                [
                    'includeSubsections' => true,
                    'homeSectionOnly' => 1.2
                ],
                [
                    'includeSubsections' => 'true',
                    'homeSectionOnly' => 'true'
                ],
                true
            ],
            [
                [
                    'includeSubsections' => true,
                    'homeSectionOnly' => 0
                ],
                [
                    'includeSubsections' => 'true',
                    'homeSectionOnly' => false
                ],
                false
            ]
        ];
    }

    /**
     * @dataProvider parametersProvider
     */
    public function testNeedsConversion($parameters, $output, $expected)
    {
        $converter = new ArticlesListParametersConverter();

        if ($expected) {
            $this->assertSame($output, $converter->convert($parameters));
        } else {
            $this->assertNotSame($output, $converter->convert($parameters));
        }
    }
}
