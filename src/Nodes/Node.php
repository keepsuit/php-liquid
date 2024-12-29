<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeRendered;

abstract class Node implements CanBeRendered
{
    protected ?int $lineNumber = null;

    public function blank(): bool
    {
        return false;
    }

    public function lineNumber(): ?int
    {
        return $this->lineNumber;
    }

    public function setLineNumber(?int $lineNumber): static
    {
        $this->lineNumber = $lineNumber;

        return $this;
    }

    /**
     * @return array<Node>
     */
    public function children(): array
    {
        return [];
    }

    public function debugLabel(): ?string
    {
        return null;
    }
}
