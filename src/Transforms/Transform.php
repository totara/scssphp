<?php

namespace ScssPhp\ScssPhp\Transforms;

/**
 * Contract for a transform action. Provided with the resource the transform can modify it in a chain.
 */
interface Transform
{
    public function execute(Resource $resource): void;
}
