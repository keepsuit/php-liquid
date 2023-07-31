<?php

namespace Keepsuit\Liquid;

use Closure;

class ParseTreeVisitor
{
    final public function __construct(
        protected mixed $node,
        /** @var array<string, Closure> */
        protected array $callbacks = []
    ) {
    }

    public static function for(mixed $root, array $callbacks = []): static
    {
        return new static($root, $callbacks);
    }

    public function addCallbackFor(string $nodeType, Closure $callback): static
    {
        $this->callbacks[$nodeType] = $callback;

        return $this;
    }

    public function visit(mixed $context = null): array
    {
        return array_map(function (mixed $node) use ($context) {
            $callback = $this->getCallbackFor($node);

            [$item, $newContext] = $callback != null ? $callback($node, $context) : [null, $context];

            return [$item, static::for($node, $this->callbacks)->visit($newContext)];
        }, $this->children());
    }

    protected function children(): array
    {
        if ($this->node instanceof HasParseTreeVisitorChildren) {
            return $this->node->parseTreeVisitorChildren();
        }

        return is_object($this->node) && method_exists($this->node, 'nodeList') ? $this->node->nodeList() : [];
    }

    protected function getCallbackFor(mixed $nodeType): ?Closure
    {
        $key = is_object($nodeType) ? get_class($nodeType) : $nodeType;

        return $this->callbacks[$key] ?? null;
    }
}
