<?php

namespace Somecode\Restify\Support\Parsers\FileParser;

class FileParserResult
{
    public function __construct(
        private array $statements,
    ) {}

    public function getStatements(): array
    {
        return $this->statements;
    }
}
