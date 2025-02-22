<?php

namespace Keepsuit\Liquid\Profiler;

use Keepsuit\Liquid\Contracts\NodeVisitor;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Tag;

class ProfilerNodeVisitor implements NodeVisitor
{
    public function __construct(
        protected bool $tags = false,
        protected bool $variables = false,
    ) {}

    public function enterNode(Node $node): void {}

    public function leaveNode(Node $node): void
    {
        if ($node instanceof Document) {
            $children = $node->body->children();
            $node->body->setChildren([
                new ProfilerDisplayStartNode(type: ProfileType::Template, name: $node->name),
                ...$children,
                new ProfilerDisplayEndNode,
            ]);

            return;
        }

        if ($node instanceof BodyNode) {
            $children = Arr::map($node->children(), function (Node $child) {
                return match (true) {
                    $this->tags && $child instanceof Tag => new BodyNode([
                        new ProfilerDisplayStartNode(type: ProfileType::Tag, name: $child->debugLabel()),
                        $child,
                        new ProfilerDisplayEndNode,
                    ]),
                    $this->variables && $child instanceof Variable => new BodyNode([
                        new ProfilerDisplayStartNode(type: ProfileType::Variable, name: $child->debugLabel()),
                        $child,
                        new ProfilerDisplayEndNode,
                    ]),
                    default => $child,
                };
            });
            $node->setChildren($children);
        }
    }
}
