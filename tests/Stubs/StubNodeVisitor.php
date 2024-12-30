<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Contracts\NodeVisitor;
use Keepsuit\Liquid\Nodes\Node;

class StubNodeVisitor implements NodeVisitor
{
    /**
     * @var Node[]
     */
    protected array $nodes = [];

    public function enterNode(Node $node): void {}

    public function leaveNode(Node $node): void
    {
        $this->nodes[] = $node;
    }

    public function getNodes(): array
    {
        return $this->nodes;
    }
}
