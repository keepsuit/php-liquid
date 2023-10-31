<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\AsyncRenderingTag;
use Keepsuit\Liquid\Tag;

class CycleTag extends Tag implements HasParseTreeVisitorChildren
{
    use AsyncRenderingTag;

    protected const SimpleSyntax = '/\A'.Regex::QuotedFragment.'+/';

    protected const NamedSyntax = '/\A('.Regex::QuotedFragment.')\s*\:\s*(.*)/m';

    protected array $variables = [];

    protected mixed $name;

    public static function tagName(): string
    {
        return 'cycle';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        if (preg_match(static::NamedSyntax, $this->markup, $matches)) {
            $this->variables = $this->parseVariablesFromString($parseContext, $matches[2]);
            $this->name = $this->parseExpression($parseContext, $matches[1]);
        } elseif (preg_match(static::SimpleSyntax, $this->markup, $matches)) {
            $this->variables = $this->parseVariablesFromString($parseContext, $this->markup);
            $this->name = json_encode($this->variables);
        } else {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.cycle'));
        }

        return $this;
    }

    public function renderAsync(Context $context): \Generator
    {
        $register = $context->getRegister('cycle') ?? [];
        assert(is_array($register));
        $key = $context->evaluate($this->name);

        $iteration = $register[$key] ?? 0;

        $value = $this->variables[$iteration];

        yield match (true) {
            is_array($value) => implode('', $value),
            default => (string) $value,
        };

        $iteration += 1;
        $iteration = $iteration >= count($this->variables) ? 0 : $iteration;

        $register[$key] = $iteration;
        $context->setRegister('cycle', $register);
    }

    public function parseTreeVisitorChildren(): array
    {
        return $this->variables;
    }

    protected function parseVariablesFromString(ParseContext $parseContext, string $markup): array
    {
        $variables = explode(',', $markup);

        $variables = array_map(
            fn (string $var) => preg_match('/\s*('.Regex::QuotedFragment.')\s*/', $var, $matches)
                ? $this->parseExpression($parseContext, $matches[1])
                : null,
            $variables
        );

        return Arr::compact($variables);
    }
}
