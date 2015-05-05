<?php

namespace Stp\SndApi\News\Validator;

use Stp\SndApi\Common\ValidatorInterface;

class ContentTypeValidator implements ValidatorInterface
{
    private $allowedArticleContentType = [
        'article',
        'person'
    ];

    private $contentType;

    /**
     * @param string $contentType
     */
    public function __construct($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return in_array($this->contentType, $this->allowedArticleContentType);
    }
}
