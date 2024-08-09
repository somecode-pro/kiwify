<?php

namespace Somecode\Restify\Support\Extractors\ValidationRules;

class ExtractedRulesResult
{
    public function __construct(
        private array $rules,
        private array $nodes
    ) {}

    public function getRules(): array
    {
        return $this->rules;
    }

    public function getNodes(): array
    {
        return $this->nodes;
    }
}
