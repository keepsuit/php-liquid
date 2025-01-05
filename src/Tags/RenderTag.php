<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Drops\ForLoopDrop;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Template;
use Traversable;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class RenderTag extends Tag implements CanBeStreamed, HasParseTreeVisitorChildren
{
    protected string|VariableLookup $templateNameExpression;

    protected mixed $variableNameExpression;

    protected ?string $aliasName;

    /**
     * @var array<string, Expression>
     */
    protected array $attributes = [];

    protected bool $isForLoop;

    public static function tagName(): string
    {
        return 'render';
    }

    public function parse(TagParseContext $context): static
    {
        $this->isForLoop = false;
        $this->variableNameExpression = null;

        $context->getParseContext()->nested(function () use ($context) {
            $templateNameExpression = $context->params->expression();
            $this->templateNameExpression = match (true) {
                is_string($templateNameExpression) => $templateNameExpression,
                $this->allowDynamicPartials() && $templateNameExpression instanceof VariableLookup => $templateNameExpression,
                default => throw new SyntaxException('Template name must be a string'),
            };

            if ($context->params->idOrFalse('for')) {
                $this->isForLoop = true;
                $this->variableNameExpression = $context->params->expression();
            } elseif ($context->params->idOrFalse('with')) {
                $this->variableNameExpression = $context->params->expression();
            }

            if ($context->params->idOrFalse('as')) {
                $aliasName = $context->params->expression();
                $this->aliasName = match (true) {
                    is_string($aliasName), $aliasName instanceof VariableLookup => (string) $aliasName,
                    default => throw new SyntaxException('Alias name must be a valid variable name'),
                };
            } else {
                $this->aliasName = null;
            }

            while ($context->params->consumeOrFalse(TokenType::Comma)) {
                $attributeName = $context->params->expression();
                if (! (is_string($attributeName) || $attributeName instanceof VariableLookup)) {
                    throw new SyntaxException('Attribute name must be a valid variable name');
                }

                $context->params->consume(TokenType::Colon);
                $attributeValue = $context->params->expression();

                $this->attributes[(string) $attributeName] = $attributeValue;
            }

            $context->params->assertEnd();

            if (is_string($this->templateNameExpression)) {
                $context->getParseContext()->loadPartial($this->templateNameExpression);
            }
        });

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $output = '';

        foreach ($this->stream($context) as $chunk) {
            $output .= $chunk;
        }

        return $output;
    }

    public function stream(RenderContext $context): \Generator
    {
        $partial = $this->loadPartial($context);
        $templateName = $partial->name() ?? '';

        $contextVariableName = $this->aliasName ?? Arr::last(explode('/', $templateName));
        assert(is_string($contextVariableName));

        $variable = $this->variableNameExpression ? $context->evaluate($this->variableNameExpression) : null;

        if ($this->isForLoop) {
            $variable = $variable instanceof Traversable ? iterator_to_array($variable) : $variable;
            assert(is_array($variable));

            $forLoop = new ForLoopDrop($templateName, count($variable));

            foreach ($variable as $value) {
                $partialContext = $this->setInnerContextVariables($context->newIsolatedSubContext($templateName), [
                    'forloop' => $forLoop,
                    $contextVariableName => $value,
                ]);

                yield from $partial->stream($partialContext);

                $forLoop->increment();
            }

            return;
        }

        $partialContext = $this->setInnerContextVariables($context->newIsolatedSubContext($templateName), [
            $contextVariableName => $variable,
        ]);

        yield from $partial->stream($partialContext);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [
            $this->templateNameExpression,
            $this->variableNameExpression,
            ...$this->attributes,
        ];
    }

    protected function loadPartial(RenderContext $context): Template
    {
        $templateName = $this->templateNameExpression;
        if ($this->allowDynamicPartials() && $this->templateNameExpression instanceof VariableLookup) {
            $templateName = $this->templateNameExpression->evaluate($context);
        }

        if (! is_string($templateName)) {
            throw new SyntaxException('Template name must be a string');
        }

        return $context->loadPartial($templateName, parseIfMissing: $this->allowDynamicPartials());
    }

    protected function setInnerContextVariables(RenderContext $context, array $variables = []): RenderContext
    {
        foreach ($variables as $key => $value) {
            $context->set($key, $value);
        }

        foreach ($this->attributes as $key => $value) {
            $context->set($key, $context->evaluate($value));
        }

        return $context;
    }

    protected function allowDynamicPartials(): bool
    {
        return false;
    }
}
