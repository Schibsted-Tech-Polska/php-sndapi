<?php

namespace Stp\SndApi\Common;

interface ConverterInterface
{
    /**
     * @param array $parameters List of parameters
     *
     * @return array
     */
    public function convert(array $parameters);
}
