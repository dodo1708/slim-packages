<?php

namespace SlimAV\Attribute;

use Attribute;

#[Attribute]
class SchemaValidate
{
    /**
     * @param $schemaPath: absolute path to the json schema or relative path from the configured schema root.
     * @param $key: Optional. If only a part of the request body should be validated. 
     */
    public function __construct(public string $schemaPath, public string $key = '')
    {
    }
}
