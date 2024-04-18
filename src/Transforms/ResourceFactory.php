<?php

namespace ScssPhp\ScssPhp\Transforms;

/**
 * A resource represents the scss file that is in the process of transforming.
 * This factory can be used to replace specific instances of resources.
 */
interface ResourceFactory
{
    /**
     * Create a new instance of the resource based on the code & path provided.
     *
     * @param string $path
     * @param string $code
     * @param callable $astParserFactory
     * @return Resource
     */
    public function createResource(string $path, string $code, callable $astParserFactory): Resource;
}
