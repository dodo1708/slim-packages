<?php

namespace SlimAP\Attribute;

use Attribute;

#[Attribute]
class PreprocessBody
{
    /**
     * @param $documentClass: Class to the document defining the preprocessing of the request body.
     * @param $key: Optional. If only a part of the request body should be preprocessed. 
     */
    public function __construct(public string $documentClass, public string $key = '')
    {
    }
}
