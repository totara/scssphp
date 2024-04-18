<?php

namespace ScssPhp\ScssPhp\Transforms;

/**
 * Default factory to create a resource.
 */
class TransformResourceFactory implements ResourceFactory
{
    /**
     * Create a new instance of the resource based on the code & path provided.
     * @param string $path
     * @param string $code
     * @param callable $astParserFactory
     * @return Resource
     */
    public function createResource(string $path, string $code, callable $astParserFactory): Resource
    {
        return new TransformResource($path, $code, $astParserFactory);
    }
}
