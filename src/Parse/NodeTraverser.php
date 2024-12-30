<?php

namespace Keepsuit\Liquid\Parse;

use Keepsuit\Liquid\Contracts\NodeVisitor;
use Keepsuit\Liquid\Nodes\Node;

class NodeTraverser
{
    public function __construct(
        /** @var array<NodeVisitor> */
        protected array $visitors = []
    ) {}

    public function addVisitor(NodeVisitor $visitor): static
    {
        $this->visitors[] = $visitor;

        return $this;
    }

    /**
     * @template TNode of Node
     *
     * @param  TNode  $node
     * @return TNode
     */
    public function traverse(Node $node): Node
    {
        foreach ($this->visitors as $visitor) {
            $this->applyVisitor($visitor, $node);
        }

        return $node;
    }

    private function applyVisitor(NodeVisitor $visitor, Node $node): void
    {
        $visitor->enterNode($node);

        foreach ($node->children() as $child) {
            $this->applyVisitor($visitor, $child);
        }

        $visitor->leaveNode($node);
    }
}
