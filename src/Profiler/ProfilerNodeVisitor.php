<?php

namespace Keepsuit\Liquid\Profiler;

use Keepsuit\Liquid\Contracts\NodeVisitor;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Literal;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\RangeLookup;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Nodes\VariableLookup;
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
                new ProfilerDisplayStartNode(type: ProfileType::Template),
                ...$children,
                new ProfilerDisplayEndNode,
            ]);

            return;
        }

        if ($node instanceof BodyNode) {
            $children = Arr::map($node->children(), function (Node $child) {
                return match (true) {
                    $this->tags && $child instanceof Tag => new BodyNode([
                        new ProfilerDisplayStartNode(type: ProfileType::Tag, name: $child::tagName()),
                        $child,
                        new ProfilerDisplayEndNode,
                    ]),
                    $this->variables && $child instanceof Variable => new BodyNode([
                        new ProfilerDisplayStartNode(type: ProfileType::Variable, name: match (true) {
                            $child->name instanceof VariableLookup => $child->name->name,
                            $child->name instanceof RangeLookup => $child->name->toString(),
                            $child->name instanceof Literal => $child->name->value,
                            is_string($child->name) => $child->name,
                            default => null,
                        }),
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
