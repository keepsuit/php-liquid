<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Arr;
use Keepsuit\Liquid\BlockBodySection;
use Keepsuit\Liquid\BreakInterrupt;
use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Drops\ForLoopDrop;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Parser;
use Keepsuit\Liquid\ParserSwitching;
use Keepsuit\Liquid\Regex;
use Keepsuit\Liquid\TagBlock;
use Keepsuit\Liquid\Tokenizer;
use Keepsuit\Liquid\TokenType;

class ForTag extends TagBlock implements HasParseTreeVisitorChildren
{
    use ParserSwitching;

    const Syntax = '/\A('.Regex::VariableSegment.'+)\s+in\s+('.Regex::QuotedFragment.'+)\s*(reversed)?/';

    protected string $variableName;

    protected mixed $collectionName;

    protected string $name;

    protected bool $reversed;

    protected mixed $from = null;

    protected mixed $limit = null;

    protected BlockBodySection $forBlock;

    protected ?BlockBodySection $elseBlock = null;

    public static function tagName(): string
    {
        return 'for';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        $this->forBlock = $this->bodySections[0];

        if (count($this->bodySections) > 1) {
            $this->elseBlock = $this->bodySections[1];
        }

        $this->strictParseWithErrorModeFallback($this->forBlock->startDelimiter()->markup ?? '', $this->parseContext);

        return $this;
    }

    protected function strictParse(string $markup): mixed
    {
        $parser = new Parser($markup);

        $this->variableName = $parser->consume(TokenType::Identifier);

        if (! $parser->idOrFalse('in')) {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.for_invalid_in'));
        }

        $collectionNameMarkup = $parser->expression();
        $this->collectionName = $this->parseExpression($collectionNameMarkup);

        $this->name = sprintf('%s-%s', $this->variableName, $collectionNameMarkup);
        $this->reversed = $parser->idOrFalse('reversed') !== false;

        while ($parser->look(TokenType::Comma) || $parser->look(TokenType::Identifier)) {
            $parser->consumeOrFalse(TokenType::Comma);

            $attribute = $parser->idOrFalse('limit') ?: $parser->idOrFalse('offset');

            if (! $attribute) {
                throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.for_invalid_attribute'));
            }

            $parser->consume(TokenType::Colon);

            $this->setAttribute($attribute, $parser->expression());
        }

        $parser->consume(TokenType::EndOfString);

        return $this;
    }

    protected function laxParse(string $markup): mixed
    {
        // TODO: Implement laxParse() method.
        throw new \RuntimeException('Not implemented yet.');
    }

    protected function setAttribute(string $attribute, string $expression): void
    {
        if ($attribute === 'offset') {
            $this->from = $expression === 'continue' ? 'continue' : $this->parseExpression($expression);

            return;
        }

        if ($attribute === 'limit') {
            $this->limit = $this->parseExpression($expression);

            return;
        }
    }

    protected function isSubTag(string $tagName): bool
    {
        return in_array($tagName, ['else'], true);
    }

    public function nodeList(): array
    {
        return $this->elseBlock ? [$this->forBlock, $this->elseBlock] : [$this->forBlock];
    }

    public function parseTreeVisitorChildren(): array
    {
        return Arr::compact([
            ...$this->nodeList(),
            $this->limit,
            $this->from,
            $this->collectionName,
        ]);
    }

    public function render(Context $context): string
    {
        $segment = $this->collectionSegment($context);

        if ($segment === []) {
            return $this->renderElse($context);
        }

        return $this->renderSegment($context, $segment);
    }

    protected function collectionSegment(Context $context): array
    {
        $offsets = $context->getRegister('for') ?? [];
        assert(is_array($offsets));

        $collection = $context->evaluate($this->collectionName) ?? [];
        $collection = $collection instanceof \Iterator ? iterator_to_array($collection) : $collection;
        assert(is_array($collection));

        if ($this->from === 'continue') {
            $offset = $offsets[$this->name];
        } else {
            $fromValue = $context->evaluate($this->from);
            $offset = $fromValue === null ? 0 : (is_numeric($fromValue) ? (int) $fromValue : throw new \InvalidArgumentException('Invalid integer'));
        }
        assert(is_int($offset));

        $limitValue = $context->evaluate($this->limit);
        $length = $limitValue === null ? null : (is_numeric($limitValue) ? (int) $limitValue : throw new \InvalidArgumentException('Invalid integer'));

        $segment = array_slice($collection, $offset, $length);
        $segment = $this->reversed ? array_reverse($segment) : $segment;

        $offsets[$this->name] = $offset + count($segment);
        $context->setRegister('for', $offsets);

        return $segment;
    }

    protected function renderSegment(Context $context, array $segment): string
    {
        /** @var ForLoopDrop[] $forStack */
        $forStack = $context->getRegister('for_stack') ?? [];
        assert(is_array($forStack));

        return $context->stack(function () use ($context, $segment, $forStack) {
            $loopVars = new ForLoopDrop(
                name: $this->name,
                length: count($segment),
                parentLoop: $forStack !== [] ? $forStack[count($forStack) - 1] : null,
            );

            $forStack[] = $loopVars;
            $context->setRegister('for_stack', $forStack);

            try {
                $context->set('forloop', $loopVars);
                $output = '';
                foreach ($segment as $value) {
                    $context->set($this->variableName, $value);
                    $output .= $this->forBlock->render($context);
                    $loopVars->increment();

                    $interrupt = $context->popInterrupt();

                    if ($interrupt instanceof BreakInterrupt) {
                        break;
                    }
                }
            } finally {
                $forStack = $context->getRegister('for_stack');
                assert(is_array($forStack));
                array_pop($forStack);
                $context->setRegister('for_stack', $forStack);
            }

            return $output;
        });
    }

    protected function renderElse(Context $context): string
    {
        return $this->elseBlock?->render($context) ?? '';
    }
}
