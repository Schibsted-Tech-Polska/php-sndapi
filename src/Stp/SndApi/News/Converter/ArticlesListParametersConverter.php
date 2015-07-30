<?php

namespace Stp\SndApi\News\Converter;

use Stp\SndApi\Common\ConverterInterface;

class ArticlesListParametersConverter implements ConverterInterface
{
    private $keysToConvertFromTypes = [
        'includeSubsections' => 'boolean',
        'homeSectionOnly' => 'boolean',
    ];

    /**
     * {@inheritdoc}
     */
    public function convert(array $parameters)
    {
        foreach ($parameters as $parameter => $value) {
            if (isset($this->keysToConvertFromTypes[$parameter])) {
                $conversionMethod = sprintf('convert%s', ucfirst($this->keysToConvertFromTypes[$parameter]));

                $parameters[$parameter] = $this->$conversionMethod($value);
            }
        }

        return $parameters;
    }

    /**
     * @param mixed $parameterValue
     *
     * @return string
     */
    protected function convertBoolean($parameterValue)
    {
        if (is_string($parameterValue)) {
            return $parameterValue;
        }

        if (is_int($parameterValue) || is_float($parameterValue)) {
            return 0 === $parameterValue ? 'false' : 'true';
        }

        return $parameterValue ? 'true' : 'false';
    }
}
