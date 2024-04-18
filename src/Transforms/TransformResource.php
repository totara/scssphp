<?php

namespace ScssPhp\ScssPhp\Transforms;

use ScssPhp\ScssPhp\Block;

/**
 * A resource represents the scss file that is in the process of transforming.
 */
class TransformResource implements Resource
{
    private bool $modified = false;
    protected ?Block $ast = null;

    /**
     * @var callable
     */
    protected $parserFactory;

    public function __construct(protected string $path, protected string $code, callable $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }

    public function getCode(): string
    {
        if ($this->modified) {
            throw new \Exception('Cannot access code as it is in AST only mode');
        }

        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
        $this->ast = null;
        $this->modified = false;
    }

    /**
     * Return the modified Tree
     *
     * @return Block
     */
    public function getAst(): Block
    {
        if ($this->ast === null) {
            if (empty($this->code)) {
                throw new \Exception('AST and source are both unavailable for this resource');
            }

            $this->ast = ($this->parserFactory)($this->path, $this->code);
        }
        return $this->ast;
    }

    public function setAst(Block $ast): void
    {
        $this->ast = $ast;
        $this->markASTModified();
    }

    /**
     * Marks the AST has been modified
     *
     * @return void
     */
    public function markASTModified(): void
    {
        $this->modified = true;
    }

    /**
     * Indicates if the resource AST has been modified or not.
     *
     * @return bool
     */
    public function isASTOnly(): bool
    {
        return $this->modified;
    }
}
