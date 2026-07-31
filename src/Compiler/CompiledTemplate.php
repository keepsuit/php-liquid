<?php

namespace Keepsuit\Liquid\Compiler;

use Closure;
use Keepsuit\Liquid\AbstractTemplate;
use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\UndefinedDropMethodException;
use Keepsuit\Liquid\Exceptions\UndefinedFilterException;
use Keepsuit\Liquid\Exceptions\UndefinedVariableException;
use Keepsuit\Liquid\Nodes\Literal;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\RangeLookup;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\TemplateSharedState;
use Throwable;

abstract class CompiledTemplate extends AbstractTemplate implements CompiledTemplateInterface
{
    public function __construct(TemplateSharedState $state = new TemplateSharedState)
    {
        parent::__construct($state);
    }

    final public function render(RenderContext $context): string
    {
        $output = '';

        foreach ($this->stream($context) as $chunk) {
            $output .= $chunk;
        }

        return $output;
    }

    /**
     * @return \Generator<string>
     */
    final public function stream(RenderContext $context): \Generator
    {
        try {
            $this->prepareContext($context);

            yield from $this->streamCompiled($context);
        } catch (LiquidException $e) {
            $this->attachTemplateName($e);
            throw $e;
        } finally {
            $this->persistContext($context);
        }
    }

    abstract public function name(): ?string;

    /**
     * @return \Generator<string>
     */
    abstract protected function streamCompiled(RenderContext $context): \Generator;

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
