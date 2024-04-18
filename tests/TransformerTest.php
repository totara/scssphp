<?php

namespace ScssPhp\ScssPhp\Tests;

use PHPUnit\Framework\TestCase;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\OutputStyle;
use ScssPhp\ScssPhp\Transforms\Resource;
use ScssPhp\ScssPhp\Transforms\TransformCompiler;
use ScssPhp\ScssPhp\Transforms\Transform;

class TransformerTest extends TestCase
{
    public function testExtractTransformsFromPath(): void
    {
        $transformer = new TransformCompiler();

        [$path, $transforms] = $transformer->extractTransformsFromPath('/a/b/c');
        $this->assertSame('/a/b/c', $path);
        $this->assertEmpty($transforms);

        [$path, $transforms] = $transformer->extractTransformsFromPath('a!/a/b/c');
        $this->assertSame('/a/b/c', $path);
        $this->assertSame(['a'], $transforms);

        [$path, $transforms] = $transformer->extractTransformsFromPath('a!b!c!/a/b/c');
        $this->assertSame('/a/b/c', $path);
        $this->assertSame(['a', 'b', 'c'], $transforms);
    }

    /**
     * Assert that transforms can be applied via code
     */
    public function testTransformCode(): void
    {
        $transform = new class () implements Transform
        {
            public function execute(Resource $resource): void
            {
                $code = $resource->getCode();
                $code = str_replace(["'", "\n"], ["\\\'", '\A'], $code);
                $resource->setCode("wrap { content: '{$code}'; }");
            }
        };

        $transformer = new TransformCompiler();
        $transformer->registerTransform('wrap', $transform);

        $compiler = new Compiler();
        $compiler->setOutputStyle(OutputStyle::COMPRESSED);
        $compiler->setTransformer($transformer);

        $dummyData = fn($url) => 'a { background: blue; }';

        $compiler->setImportPaths([$dummyData]);
        $compiler->setFileLoader($dummyData);

        $input = '@import "wrap!my_file"; a { color: blue; }';
        $result = $compiler->compileString($input)->getCss();
        $this->assertSame('wrap{content:"a { background: blue; }"}a{color:blue}', $result);
    }

    /**
     * Assert that a custom transformer can be applied
     */
    public function testCustomTransform(): void
    {
        $transform = new class () implements Transform
        {
            public function execute(Resource $resource): void
            {
                $resource->getAst()->children[0][1] = '/* TestTestD */';
            }
        };

        $transformer = new TransformCompiler();
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
