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
        try {
            $this->name = null;
            $this->variables = [];

            if ($context->params->look(TokenType::Colon, 1)) {
                $currentToken = $context->params->current();

                $name = $context->params->expression();
                $this->name = match (true) {
                    is_string($name), is_numeric($name), $name instanceof VariableLookup => (string) $name,
                    $currentToken === null => throw SyntaxException::unexpectedEndOfTemplate(),
                    default => throw SyntaxException::unexpectedToken($currentToken),
                };

                $context->params->consumeRaw(TokenType::Colon);
            }

            do {
                $currentToken = $context->params->current();

                if (! $currentToken) {
                    throw SyntaxException::unexpectedEndOfTemplate();
                }

                if (! in_array($currentToken->type, [TokenType::String, TokenType::Number])) {
                    throw SyntaxException::unexpectedToken($currentToken);
                }

                $variable = $context->params->expression();
                $this->variables[] = match (true) {
                    is_string($variable), is_numeric($variable) => $variable,
                    default => throw SyntaxException::unexpectedToken($currentToken)
                };
            } while ($context->params->consumeIf(TokenType::Comma));

            if ($this->name === null) {
                $this->name = json_encode($this->variables, JSON_THROW_ON_ERROR);
            }

            $context->params->assertEnd();
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException(static::tagName(), 'cycle [<name>:] <value>[, <value>...]', $e);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $output = '';

        $register = $context->getRegister('cycle') ?? [];
        assert(is_array($register));
        $key = $context->evaluate($this->name);
        assert(is_string($key) || is_int($key));

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
