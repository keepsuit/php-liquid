<?php

namespace Keepsuit\Liquid\Compiler;

use Closure;
use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Literal;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\RangeLookup;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\TemplateSharedState;
use Throwable;

class CompiledTemplate extends Template implements CompiledTemplateInterface
{
    /**
     * @param  Closure(RenderContext): string|null  $renderer
     * @param  Closure(RenderContext): \Generator<string>|null  $streamer
     */
    public function __construct(
        Document $root,
        ?TemplateSharedState $state = null,
        protected readonly ?Closure $renderer = null,
        protected readonly ?Closure $streamer = null,
    ) {
        parent::__construct($root, $state ?? new TemplateSharedState);
    }

    public function render(RenderContext $context): string
    {
        if ($this->renderer === null) {
            return parent::render($context);
        }

        try {
            $context->mergeOutputs($this->state->outputs);

            return ($this->renderer)($context);
        } catch (\Keepsuit\Liquid\Exceptions\LiquidException $e) {
            $e->templateName = $e->templateName ?? $this->root->name;
            throw $e;
        } finally {
            $this->state->errors = $context->getErrors();
            $this->state->outputs = $context->getOutputs();
        }
    }

    public function stream(RenderContext $context): \Generator
    {
        if ($this->streamer === null) {
            yield from parent::stream($context);

            return;
        }

        try {
            $context->mergeOutputs($this->state->outputs);

            /** @var \Generator<string> $stream */
            $stream = ($this->streamer)($context);

            yield from $stream;
        } catch (\Keepsuit\Liquid\Exceptions\LiquidException $e) {
            $e->templateName = $e->templateName ?? $this->root->name;
            throw $e;
        } finally {
            $this->state->errors = $context->getErrors();
            $this->state->outputs = $context->getOutputs();
        }
    }

    public static function decodeValue(string $payload): mixed
    {
        $serialized = base64_decode($payload, true);

        if ($serialized === false) {
            throw new \RuntimeException('Invalid compiler value encoding.');
        }

        set_error_handler(static function (int $severity, string $message): never {
            throw new \RuntimeException($message, $severity);
        });

        try {
            $value = unserialize($serialized, ['allowed_classes' => true]);
        } finally {
            restore_error_handler();
        }

        if ($value === false && $serialized !== 'b:0;') {
            throw new \RuntimeException('Invalid serialized compiler value.');
        }

        self::assertDecodedValue($value);

        return $value;
    }

    /**
     * @param  array<int,true>  $seenObjects
     */
    private static function assertDecodedValue(mixed $value, int $depth = 0, array &$seenObjects = []): void
    {
        if ($depth > 256 || is_resource($value)) {
            throw new \RuntimeException('Unsafe decoded compiler value.');
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                self::assertDecodedValue($item, $depth + 1, $seenObjects);
            }

            return;
        }

        if (! is_object($value)) {
            return;
        }

        if (get_class($value) === '__PHP_Incomplete_Class') {
            throw new \RuntimeException('Incomplete decoded compiler class.');
        }

        $objectId = spl_object_id($value);

        if (isset($seenObjects[$objectId])) {
            return;
        }

        $seenObjects[$objectId] = true;

        foreach ((array) $value as $property) {
            self::assertDecodedValue($property, $depth + 1, $seenObjects);
        }
    }

    public static function renderCompiledBody(RenderContext $context, Closure $renderer, int $childCount): string
    {
        $context->resourceLimits->incrementRenderScore($childCount);
        $output = $renderer($context);
        $context->resourceLimits->incrementWriteScore($output);

        return $output;
    }

    public static function renderVariable(
        RenderContext $context,
        bool|float|int|Literal|RangeLookup|VariableLookup|string|null $name,
        array $filters,
        ?int $lineNumber,
    ): string {
        try {
            return (new Variable($name, $filters))->render($context);
        } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
            $context->handleError($exception, $lineNumber);

            return '';
        } catch (Throwable $exception) {
            return $context->handleError($exception, $lineNumber);
        }
    }

    public static function renderNode(RenderContext $context, Node $node, ?int $lineNumber): string
    {
        try {
            if ($node instanceof Disableable && $node instanceof Tag) {
                $node->ensureTagIsEnabled($context);
            }

            return $node->render($context);
        } catch (UndefinedVariableException|UndefinedDropMethodException|UndefinedFilterException $exception) {
            $context->handleError($exception, $lineNumber);

            return '';
        } catch (Throwable $exception) {
            return $context->handleError($exception, $lineNumber);
        }
    }
}
