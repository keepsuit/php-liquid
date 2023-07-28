<?php

namespace Keepsuit\Liquid;

class Condition implements HasParseTreeVisitorChildren
{
    public function __construct(
        protected mixed $left = null,
        protected ?string $operator = null,
        protected mixed $right = null
    ) {
    }

    public function parseTreeVisitorChildren(): array
    {
        return Arr::compact([
            $this->left,
            $this->right,
            //$this->childCondition,
            //$this->attachment
        ]);
    }
}
