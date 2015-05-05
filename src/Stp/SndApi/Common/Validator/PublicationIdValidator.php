<?php

namespace Stp\SndApi\Common\Validator;

use Stp\SndApi\Common\ValidatorInterface;

class PublicationIdValidator implements ValidatorInterface
{
    private static $allowedPublicationsId = [
        'common',
        'sa',
        'fvn',
        'bt',
        'ap',
    ];

    private $publicationId;

    /**
     * @param int $publicationId
     */
    public function __construct($publicationId)
    {
        $this->publicationId = $publicationId;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return in_array($this->publicationId, self::$allowedPublicationsId);
    }
}
