<?php

namespace Somecode\Restify\Support\Parsers\FileParser;

use Illuminate\Support\Arr;
use PhpParser\ParserFactory;

class FileParser
{
    public static function parse(string $fileContent): FileParserResult
    {
        $statements = Arr::wrap(
            (new ParserFactory())->createForHostVersion()->parse($fileContent)
        );

        return new FileParserResult($statements);
    }
}
