<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Drops\ForLoopDrop;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\ExpressionParser;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Template;
use Traversable;

/**
 * @phpstan-import-type Expression from ExpressionParser
 */
class RenderTag extends Tag implements HasParseTreeVisitorChildren
{
    protected string $templateNameExpression;

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

            $context->getParseContext()->loadPartial($this->templateNameExpression);
        });

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $partial = $context->loadPartial($this->templateNameExpression);

        $contextVariableName = $this->aliasName ?? Arr::last(explode('/', $this->templateNameExpression));
        assert(is_string($contextVariableName));

        $variable = $this->variableNameExpression ? $context->evaluate($this->variableNameExpression) : null;

        if ($this->isForLoop) {
            $variable = $variable instanceof Traversable ? iterator_to_array($variable) : $variable;
            assert(is_array($variable));

            $forLoop = new ForLoopDrop($this->templateNameExpression, count($variable));

            $output = '';
            foreach ($variable as $value) {
                $partialContext = $this->buildPartialContext($partial, $context, [
                    'forloop' => $forLoop,
                    $contextVariableName => $value,
                ]);

                $output .= $partial->render($partialContext);

                $forLoop->increment();
            }

            return $output;
        }

        $partialContext = $this->buildPartialContext($partial, $context, [
            $contextVariableName => $variable,
        ]);

        return $partial->render($partialContext);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [
            $this->templateNameExpression,
            $this->variableNameExpression,
            ...$this->attributes,
        ];
    }

    protected function buildPartialContext(Template $partial, RenderContext $context, array $variables = []): RenderContext
    {
        $innerContext = $context->newIsolatedSubContext($partial->name);

        foreach ($variables as $key => $value) {
            $innerContext->set($key, $value);
        }

        foreach ($this->attributes as $key => $value) {
            $innerContext->set($key, $context->evaluate($value));
        }

        return $innerContext;
    }
}
