<?php

namespace ScssPhp\ScssPhp\Transforms;

use ScssPhp\ScssPhp\Block;

/**
 * Base class that applies the transformation actions, an extension to the compiler.
 */
class TransformCompiler
{
    protected ResourceFactory $resourceFactory;

    /**
     * @param array<string, Transform> $transforms
     */
    public function __construct(protected array $transforms = [], ?ResourceFactory $resourceFactory = null)
    {
        $this->resourceFactory = $resourceFactory ?? new TransformResourceFactory();
    }

    public function registerTransform(string $name, Transform $transform): void
    {
        $this->transforms[$name] = $transform;
    }

    /**
     * @param string[] $transforms
     * @param string $path
     * @param string $code
     * @param callable $astParserFactory
     * @return Block
     */
    public function applyTransformations(array $transforms, string $path, string $code, callable $astParserFactory): Block
    {
        // Make the resource
        $resource = $this->resourceFactory->createResource($path, $code, $astParserFactory);

        // transforms execute from right to left (like webpack)
        $transforms = array_reverse($transforms, true);
        foreach ($transforms as $name) {
            if (!isset($this->transforms[$name])) {
                throw new \Exception('Unknown transform "' . $name . '"');
            }
            $this->transforms[$name]->execute($resource);
        }

        return $resource->getAst();
    }

    /**
     * @param string $path
     * @return array{0: string, 1: string[]}
     */
    public function extractTransformsFromPath(string $path): array
    {
        $pos = strrpos($path, '!');
        $transforms = [];

        if ($pos !== false) {
            $pathTransforms = substr($path, 0, $pos);
            $transforms = !empty($pathTransforms) ? explode('!', $pathTransforms) : [];
            $path = substr($path, $pos + 1);
        }
        return [$path, $transforms];
    }
}
