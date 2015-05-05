<?php

namespace Stp\SndApi\News\Validator;

use Stp\SndApi\Common\ValidatorInterface;

class ArticleListMethodValidator implements ValidatorInterface
{
    private $allowedMethods = [
        'desked',
        'auto'
    ];

    private $method;

    /**
     * @param string $method
     */
    public function __construct($method)
    {
        $this->method = $method;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return in_array($this->method, $this->allowedMethods);
    }
}
