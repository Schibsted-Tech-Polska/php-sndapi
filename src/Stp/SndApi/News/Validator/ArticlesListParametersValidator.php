<?php

namespace Stp\SndApi\News\Validator;

use DateTime;
use Stp\SndApi\Common\ValidatorInterface;

class ArticlesListParametersValidator implements ValidatorInterface
{
    private $allowedMethodsParameters = [
        'desked' => [
            'areaLimit',
            'offset',
            'limit'
        ],
        'auto' => [
            'offset',
            'limit',
            'since',
            'until',
            'contentType',
            'includeSubsections',
            'homeSectionOnly'
        ]
    ];

    private $allowedParameterTypes = [
        'includeSubsections' => 'boolean',
        'homeSectionOnly' => 'boolean',
    ];

    private $method;

    private $parameters;

    /**
     * @param string $method
     * @param array $parameters
     */
    public function __construct($method, $parameters)
    {
        $this->method = $method;
        $this->parameters = $parameters;
    }

    private function validateBooleanString($value)
    {
        return $value === 'true' || $value === 'false';
    }

    private function entryIsValid($key, $value)
    {
        if (!in_array($key, $this->allowedMethodsParameters[$this->method])) {
            return false;
        }

        if (!array_key_exists($key, $this->allowedParameterTypes)) {
            return true;
        }

        return $this->validateBooleanString($value);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        foreach ($this->parameters as $parameterName => $parameterValue) {
            if (!$this->entryIsValid($parameterName, $parameterValue)) {
                return false;
            }
        }

        return true;
    }
}
