<?php

namespace Somecode\Restify\Support\Reflectors;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use ReflectionMethod;
use Somecode\Restify\Support\Parsers\FileParser\FileParser;

class MethodReflector
{
    private ?ClassMethod $node = null;

    public function __construct(
        private string $className,
        private string $name
    ) {}

    public static function create(string $className, string $name): static
    {
        return new static($className, $name);
    }

    public function getReflection(): ReflectionMethod
    {
        return new ReflectionMethod($this->className, $this->name);
    }

    public function getMethodCode(): string
    {
        $reflection = $this->getReflection();

        $filePath = $reflection->getFileName();

        if ($filePath === false || ! file_exists($filePath)) {
            return '';
        }

        $fileContent = file($filePath, FILE_IGNORE_NEW_LINES);

        if ($fileContent === false) {
            return '';
        }

        $length = $reflection->getEndLine() - $reflection->getStartLine() + 1;
        $methodCodeLines = array_slice($fileContent, $reflection->getStartLine() - 1, $length);

        return implode("\n", $methodCodeLines);
    }

    public function getAstNode(): ?ClassMethod
    {
        if ($this->node === null) {
            $partialClass = $this->buildPartialClass();
            $this->node = $this->findMethodNode($partialClass);
        }

        return $this->node;
    }

    private function buildPartialClass(): string
    {
        $className = $this->getClassName();
        $methodDoc = $this->getMethodDocComment();
        $lines = $this->calculateEmptyLines($methodDoc);
        $methodCode = $this->getMethodCode();

        return "<?php\n{$lines}class {$className} {\n{$methodDoc}\n{$methodCode}\n}";
    }

    private function getClassName(): string
    {
        return class_basename($this->className);
    }

    private function getMethodDocComment(): string
    {
        $methodReflection = $this->getReflection();

        return $methodReflection->getDocComment() ?: '';
    }

    private function calculateEmptyLines(string $methodDoc): string
    {
        $methodReflection = $this->getReflection();
        $startLine = $methodReflection->getStartLine();
        $emptyLines = max($startLine - 3 - substr_count($methodDoc, "\n"), 1);

        return str_repeat("\n", $emptyLines);
    }

    private function findMethodNode(string $partialClass): ?ClassMethod
    {
        try {
            $statements = FileParser::parse($partialClass)->getStatements();
        } catch (\Exception) {
            return null;
        }

        $node = (new NodeFinder)->findFirst(
            $statements,
            fn (Node $node) => $node instanceof Node\Stmt\ClassMethod && $node->name->name === $this->name
        );

        $traverser = new NodeTraverser;

        $traverser->addVisitor(new class($this->getClassReflector()->getNameContext()) extends NameResolver
        {
            public function __construct($nameContext)
            {
                parent::__construct();
                $this->nameContext = $nameContext;
            }

            public function beforeTraverse(array $nodes): ?array
            {
                return null;
            }
        });

        $traverser->traverse([$node]);

        return $node;
    }

    public function getClassReflector(): ClassReflector
    {
        return ClassReflector::create($this->className);
    }
}
