<?php

namespace Stp\SndApi\News\Validator;

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

    /**
     * @return bool
     */
    public function isValid()
    {
        $parameterKeys = array_keys($this->parameters);

        foreach ($parameterKeys as $parameter) {
            if (!in_array($parameter, $this->allowedMethodsParameters[$this->method])) {
                return false;
            }
        }

        return true;
    }
}
