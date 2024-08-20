<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class CycleTag extends Tag implements HasParseTreeVisitorChildren
{
    protected const SYNTAX_ERROR = "Syntax Error in 'cycle' - Valid syntax: cycle [name :] var [, var2, var3 ...]";

    /**
     * @var (string|int|float)[]
     */
    protected array $variables = [];

    protected ?string $name = null;

    public static function tagName(): string
    {
        return 'cycle';
    }

    public function parse(TagParseContext $context): static
    {
        $this->name = null;
        $this->variables = [];

        if ($context->params->look(TokenType::Colon, 1)) {
            if (! in_array($context->params->current()?->type, [TokenType::String, TokenType::Number, TokenType::Identifier])) {
                throw new SyntaxException(self::SYNTAX_ERROR);
            }

            $name = $context->params->expression();
            $this->name = match (true) {
                is_string($name), is_numeric($name), $name instanceof VariableLookup => (string) $name,
                default => throw new SyntaxException(self::SYNTAX_ERROR),
            };

            $context->params->consume(TokenType::Colon);
        }

        do {
            if (! in_array($context->params->current()?->type, [TokenType::String, TokenType::Number])) {
                throw new SyntaxException(self::SYNTAX_ERROR);
            }

            $variable = $context->params->expression();
            $this->variables[] = match (true) {
                is_string($variable), is_numeric($variable) => $variable,
                default => throw new SyntaxException(self::SYNTAX_ERROR),
            };
        } while ($context->params->consumeOrFalse(TokenType::Comma));

        if ($this->name === null) {
            $this->name = json_encode($this->variables, JSON_THROW_ON_ERROR);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $output = '';

        $register = $context->getRegister('cycle') ?? [];
        assert(is_array($register));
        $key = $context->evaluate($this->name);

        $iteration = match (true) {
            isset($register[$key]) && is_int($register[$key]) => $register[$key],
            default => 0,
        };

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
