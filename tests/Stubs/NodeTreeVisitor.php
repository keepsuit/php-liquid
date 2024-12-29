<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Contracts\NodeVisitor;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Nodes\Text;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Tag;

class NodeTreeVisitor implements NodeVisitor
{
    /**
     * @var NodeTreeItem[]
     */
    protected array $rootNodes = [];

    /**
     * @var NodeTreeItem[]
     */
    protected array $activeNodes = [];

    public function enterNode(Node $node): void
    {
        if ($node instanceof BodyNode) {
            return;
        }

        $element = $this->buildItem($node);

        if (count($this->activeNodes) === 0) {
            array_unshift($this->rootNodes, $element);
        } else {
            $active = $this->activeNodes[0];
            $active->children[] = $element;
        }

        array_unshift($this->activeNodes, $element);
    }

    public function leaveNode(Node $node): void
    {
        if ($node instanceof BodyNode) {
            return;
        }

        array_shift($this->activeNodes);
    }

    /**
     * @return NodeTreeItem[]
     */
    public function getTrees(): array
    {
        return $this->rootNodes;
    }

    public function reset(): void
    {
        $this->rootNodes = [];
        $this->activeNodes = [];
    }

    protected function buildItem(Node $node): NodeTreeItem
    {
        $type = match (true) {
            $node instanceof Document => 'document',
            $node instanceof BodyNode => 'body',
            $node instanceof Tag => 'tag',
            $node instanceof Variable => 'variable',
            $node instanceof Range => 'range',
            $node instanceof Raw => 'raw',
            $node instanceof Text => 'text',
            default => Node::class,
        };

        return new NodeTreeItem($type, $node->debugLabel());
    }
}
