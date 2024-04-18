<?php

namespace ScssPhp\ScssPhp\Transforms;

use ScssPhp\ScssPhp\Block;

class ResourceFactory {
    /**
     * Create a new resource instance to be used in transformations.
     *
     * @return Resource
     */
    public function createResource(string $path, Block $ast): Resource {
        return new Resource($path, $ast);
    }
}
