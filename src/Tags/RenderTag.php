<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Drops\ForLoopDrop;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Template;

class RenderTag extends Tag implements HasParseTreeVisitorChildren
{
    protected string $templateNameExpression;

    protected mixed $variableNameExpression = '';

    protected ?string $aliasName = null;

    protected array $attributes = [];

    protected bool $isForLoop = false;

    public static function tagName(): string
    {
        return 'render';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        $parseContext->nested(function () use ($parseContext) {
            $parser = $this->newParser();

            $templateNameExpression = $this->parseExpression($parser->consume(TokenType::String));
            assert(is_string($templateNameExpression));
            $this->templateNameExpression = $templateNameExpression;

            if ($parser->consumeOrFalse(TokenType::EndOfString) !== false) {
                $parseContext->loadPartial($this->templateNameExpression);

                return;
            }

            $this->isForLoop = $parser->idOrFalse('for') !== false;
            if ($this->isForLoop || $parser->idOrFalse('with') !== false) {
                $this->variableNameExpression = $this->parseExpression($parser->expression());
                $this->aliasName = $parser->idOrFalse('as') ? $parser->consume(TokenType::Identifier) : null;
            }

            while ($parser->consumeOrFalse(TokenType::Comma) !== false) {
                $attribute = $parser->consume(TokenType::Identifier);
                $parser->consume(TokenType::Colon);
                $this->attributes[$attribute] = $this->parseExpression($parser->expression());
            }

            $parser->consume(TokenType::EndOfString);

            $parseContext->loadPartial($this->templateNameExpression);
        });

        return $this;
    }

    public function render(Context $context): string
    {
        //        dd($this);
        $partial = $context->loadPartial($this->templateNameExpression);

        $contextVariableName = $this->aliasName ?? Arr::last(explode('/', $this->templateNameExpression));
        assert(is_string($contextVariableName));

        $variable = $this->variableNameExpression ? $context->evaluate($this->variableNameExpression) : null;

        if ($this->isForLoop) {
            $variable = $variable instanceof \Traversable ? iterator_to_array($variable) : $variable;
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
