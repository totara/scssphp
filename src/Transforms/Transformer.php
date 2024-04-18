<?php

namespace ScssPhp\ScssPhp\Transforms;

use ScssPhp\ScssPhp\Block;

class Transformer {

    public function __construct(protected ?ResourceFactory $factory = null, protected array $transforms = []) {

    }

    public function setResourceFactory(?ResourceFactory $factory): void {
        $this->factory = $factory;
    }

    public function registerTransform(string $name, Transform $transform): void {
        $this->transforms[$name] = $transform;
    }

    public function applyTransformations(array $transforms, string $path, Block $tree): Block {
        // Make the resource
        $resource = ($this->factory ?? new ResourceFactory())->createResource($path, $tree);

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
     * @param bool $split
     * @return array<string, array|string>
     */
    public function extractTransformsFromPath(string $path, bool $split = false): array {
        $pos = strrpos($path, '!');
        $transforms = "";

        if ($pos !== false) {
            $transforms = substr($path, 0, $pos);
            $path = substr($path, $pos + 1);
        }

        if ($split) {
            $transforms = empty($transforms) ? [] : explode('!', $transforms);
        }

        return [$path, $transforms];
    }
}
