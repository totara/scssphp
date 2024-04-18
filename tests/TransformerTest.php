<?php

namespace ScssPhp\ScssPhp\Tests;

use PHPUnit\Framework\TestCase;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use ScssPhp\ScssPhp\Transforms\Resource;
use ScssPhp\ScssPhp\Transforms\Transform;
use ScssPhp\ScssPhp\Transforms\Transformer;

class TransformerTest extends TestCase {

    public function testExtractTransformsFromPath(): void {
        $transformer = new Transformer();

        [$path, $transforms] = $transformer->extractTransformsFromPath('/a/b/c');
        $this->assertSame('/a/b/c', $path);
        $this->assertEmpty($transforms);

        [$path, $transforms] = $transformer->extractTransformsFromPath('a!/a/b/c');
        $this->assertSame('/a/b/c', $path);
        $this->assertSame('a', $transforms);

        [$path, $transforms] = $transformer->extractTransformsFromPath('a!b!c!/a/b/c');
        $this->assertSame('/a/b/c', $path);
        $this->assertSame('a!b!c', $transforms);

        [$path, $transforms] = $transformer->extractTransformsFromPath('/a/b/c', true);
        $this->assertSame('/a/b/c', $path);
        $this->assertIsArray($transforms);
        $this->assertEmpty($transforms, var_export($transforms, true));

        [$path, $transforms] = $transformer->extractTransformsFromPath('a!/a/b/c', true);
        $this->assertSame('/a/b/c', $path);
        $this->assertIsArray($transforms);
        $this->assertSame(['a'], $transforms);

        [$path, $transforms] = $transformer->extractTransformsFromPath('a!b!c!/a/b/c', true);
        $this->assertSame('/a/b/c', $path);
        $this->assertIsArray($transforms);
        $this->assertSame(['a', 'b', 'c'], $transforms);
    }

    /**
     * Assert that a custom transformer can be applied
     */
    public function testCustomTransform(): void {
        $transform = new class() implements Transform {
            public function execute(Resource $resource): void {
                $resource->getAst()->children[0][1] = '/* TestTestD */';
            }
        };

        $transformer = new Transformer();
        $transformer->registerTransform('test', $transform);

        $compiler = new Compiler();
        $compiler->setOutputStyle(OutputStyle::EXPANDED);
        $compiler->setTransformer($transformer);

        $dummyData = fn($url) => '/* TestC */ a { color: red; }';

        $compiler->setImportPaths([$dummyData]);
        $compiler->setFileLoader($dummyData);

        $input = '@import "test!my_file"; a { color: blue; }';
        $expected = <<<HERE
/* TestTestD */
a {
  color: red;
}
a {
  color: blue;
}

HERE;

        $result = $compiler->compileString($input)->getCss();
        $this->assertSame($expected, $result);

        $compiler->setOutputStyle(OutputStyle::COMPRESSED);
        $result = $compiler->compileString($input)->getCss();
        $this->assertSame('a{color:red}a{color:blue}', $result);

        $input = '@import "my_file"; a { color: blue; }';
        $compiler->setOutputStyle(OutputStyle::EXPANDED);
        $expected = <<<HERE
/* TestC */
a {
  color: red;
}
a {
  color: blue;
}

HERE;
        $result = $compiler->compileString($input)->getCss();
        $this->assertSame($expected, $result);
    }

}
