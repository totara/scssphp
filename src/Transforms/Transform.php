<?php

namespace ScssPhp\ScssPhp\Transforms;

interface Transform {
    /**
     * @param Resource $resource
     * @return void
     */
    public function execute(\ScssPhp\ScssPhp\Transforms\Resource $resource): void;
}
