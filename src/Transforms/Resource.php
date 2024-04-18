<?php

namespace ScssPhp\ScssPhp\Transforms;

use ScssPhp\ScssPhp\Block;

/**
 * A resource represents the scss file that is in the process of transforming.
 */
interface Resource
{
    /**
     * Reads back the specific code if it's still in a readable state.
     * If the AST has been generated an exception will be thrown instead.
     */
    public function getCode(): string;

    /**
     * Set the CSS code to transform. This will delete the internal AST tree, marking it ready for parsing again.
     */
    public function setCode(string $code): void;

    /**
     * Load the compiled tree.
     */
    public function getAst(): Block;

    public function setAst(Block $ast): void;

    public function markASTModified(): void;

    public function isASTOnly(): bool;
}
