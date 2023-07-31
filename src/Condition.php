<?php

namespace Keepsuit\Liquid;

class Condition implements HasParseTreeVisitorChildren
{
    protected ?ConditionsRelation $childRelation = null;

    protected ?Condition $childCondition = null;

    public ?BlockBody $attachment = null;

    public function __construct(
        protected mixed $left = null,
        protected ?string $operator = null,
        protected mixed $right = null
    ) {
    }

    public function and(Condition $childCondition): static
    {
        $this->childRelation = ConditionsRelation::And;
        $this->childCondition = $childCondition;

        return $this;
    }

    public function or(Condition $childCondition): static
    {
        $this->childRelation = ConditionsRelation::Or;
        $this->childCondition = $childCondition;

        return $this;
    }

    public function attach(?BlockBody $body): Condition
    {
        $this->attachment = $body;

        return $this;
    }

    public function parseTreeVisitorChildren(): array
    {
        return Arr::compact([
            $this->left,
            $this->right,
            $this->childCondition,
            $this->attachment,
        ]);
    }
}
