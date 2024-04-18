<?php

namespace ScssPhp\ScssPhp\Transforms;

use ScssPhp\ScssPhp\Block;

/**
 * A resource represents the scss file that is in the process of transforming.
 */
class Resource {
    private bool $modified = false;

    public function __construct(protected string $path, protected Block $ast) {

    }

    /**
     * Return the modified Tree
     *
     * @return Block
     */
    public function getAst(): Block {
        return $this->ast;
    }

    /**
     * Marks the AST has been modified
     *
     * @return void
     */
    public function markASTModified(): void {
        $this->modified = true;
    }

    /**
     * Indicates if the resource AST has been modified or not.
     *
     * @return bool
     */
    public function isASTOnly(): bool {
        return $this->modified;
    }
}
