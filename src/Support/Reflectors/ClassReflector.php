<?php

namespace Somecode\Restify\Support\Reflectors;

use Illuminate\Support\Str;
use PhpParser\NameContext;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use ReflectionClass;
use Somecode\Restify\Support\Parsers\FileParser\FileParser;

class ClassReflector
{
    private ?NameContext $nameContext = null;

    private array $methods = [];

    private function __construct(
        private string $className,
    ) {}

    public static function create(string $className): static
    {
        return new static($className);
    }

    public function getReflection(): ReflectionClass
    {
        return new ReflectionClass($this->className);
    }

    public function getNameContext(): NameContext
    {
        if (! $this->nameContext) {
            $content = ($path = $this->getReflection()->getFileName())
                ? file_get_contents($path)
                : "<? class {$this->className} {}";

            preg_match(
                '/(class|enum|interface|trait)\s+?(.*?)\s+?{/m',
                $content,
                $matches,
            );

            $firstMatchedClassLikeString = $matches[0] ?? '';

            $code = Str::before($content, $firstMatchedClassLikeString);

            // Removes all comments.
            $code = preg_replace('/\/\*(?:[^*]|\*+[^*\/])*\*+\/|(?<![:\'"])\/\/.*|(?<![:\'"])#.*/', '', $code);

            $re = '/(namespace|use) ([.\s\S]*?);/m';
            preg_match_all($re, $code, $matches);

            $code = "<?php\n".implode("\n", $matches[0]);

            $nodes = FileParser::parse($code)->getStatements();

            $traverser = new NodeTraverser;
            $traverser->addVisitor($nameResolver = new NameResolver);
            $traverser->traverse($nodes);

            $this->nameContext = $nameResolver->getNameContext();
        }

        return $this->nameContext;
    }
}
