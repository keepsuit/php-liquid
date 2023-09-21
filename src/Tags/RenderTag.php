<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Drops\ForLoopDrop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\AsyncRenderingTag;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Template;
use Traversable;

class RenderTag extends Tag implements HasParseTreeVisitorChildren
{
    use AsyncRenderingTag;

    protected const Syntax = '/('.Regex::QuotedString.'+)(\s+(with|for)\s+('.Regex::QuotedFragment.'+))?(\s+(?:as)\s+('.Regex::VariableSegment.'+))?/';

    protected string $templateNameExpression;

    protected mixed $variableNameExpression;

    protected ?string $aliasName;

    protected array $attributes = [];

    protected bool $isForLoop;

    public static function tagName(): string
    {
        return 'render';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        return $parseContext->nested(function () use ($parseContext) {
            if (! preg_match(static::Syntax, $this->markup, $matches)) {
                throw new SyntaxException($parseContext->locale->translate('errors.syntax.render'));
            }

            $templateNameExpression = $this->parseExpression($parseContext, $matches[1]);
            if (! is_string($templateNameExpression)) {
                throw new InvalidArgumentException('Template name must be a string');
            }
            $this->templateNameExpression = $templateNameExpression;

            $this->aliasName = $matches[6] ?? null;
            $this->variableNameExpression = ($matches[4] ?? null) ? $this->parseExpression($parseContext, $matches[4]) : null;
            $this->isForLoop = ($matches[3] ?? null) === 'for';

            preg_match_all(sprintf('/%s/', Regex::TagAttributes), $this->markup, $attributeMatches, PREG_SET_ORDER);
            foreach ($attributeMatches as $matches) {
                $this->attributes[$matches[1]] = $this->parseExpression($parseContext, $matches[2]);
            }

            $parseContext->loadPartial($this->templateNameExpression);

            return $this;
        });
    }

    public function renderAsync(Context $context): \Generator
    {
        $partial = $context->loadPartial($this->templateNameExpression);

        $contextVariableName = $this->aliasName ?? Arr::last(explode('/', $this->templateNameExpression));
        assert(is_string($contextVariableName));

        $variable = $this->variableNameExpression ? $context->evaluate($this->variableNameExpression) : null;

        if ($this->isForLoop) {
            $variable = $variable instanceof Traversable ? iterator_to_array($variable) : $variable;
            assert(is_array($variable));

            $forLoop = new ForLoopDrop($this->templateNameExpression, count($variable));

            foreach ($variable as $value) {
                $partialContext = $this->buildPartialContext($partial, $context, [
                    'forloop' => $forLoop,
                    $contextVariableName => $value,
                ]);

                yield from $partial->renderAsync($partialContext);

                $forLoop->increment();
            }

            return;
        }

        $partialContext = $this->buildPartialContext($partial, $context, [
            $contextVariableName => $variable,
        ]);

        yield from $partial->renderAsync($partialContext);
    }

    public function parseTreeVisitorChildren(): array
    {
        return [
            $this->templateNameExpression,
            $this->variableNameExpression,
            ...$this->attributes,
        ];
    }

    protected function buildPartialContext(Template $partial, Context $context, array $variables = []): Context
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
