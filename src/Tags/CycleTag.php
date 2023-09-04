<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tag;

class CycleTag extends Tag implements HasParseTreeVisitorChildren
{
    protected array $variables = [];

    protected mixed $name = null;

    public static function tagName(): string
    {
        return 'cycle';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        try {
            $parser = $this->newParser();

            if ($parser->look(TokenType::Colon, 1)) {
                $this->name = $this->parseExpression($parser->expression());
                $parser->consume(TokenType::Colon);
            }

            $this->variables = [];
            do {
                $this->variables[] = $this->parseExpression($parser->expression());
            } while ($parser->consumeOrFalse(TokenType::Comma));

            if ($this->name === null) {
                $this->name = json_encode($this->variables);
            }
        } catch (SyntaxException $exception) {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.cycle'), previous: $exception);
        }

        return $this;
    }

    public function render(Context $context): string
    {
        $output = '';

        $register = $context->getRegister('cycle') ?? [];
        assert(is_array($register));
        $key = $context->evaluate($this->name);

        $iteration = $register[$key] ?? 0;

        $value = $this->variables[$iteration];

        $value = match (true) {
            is_array($value) => implode('', $value),
            default => (string) $value,
        };

        $output .= $value;

        $iteration += 1;
        $iteration = $iteration >= count($this->variables) ? 0 : $iteration;

        $register[$key] = $iteration;
        $context->setRegister('cycle', $register);

        return $output;
    }

    public function parseTreeVisitorChildren(): array
    {
        return $this->variables;
    }
}
