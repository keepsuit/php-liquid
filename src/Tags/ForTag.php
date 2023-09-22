<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Drops\ForLoopDrop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Interrupts\BreakInterrupt;
use Keepsuit\Liquid\Nodes\BlockBodySection;
use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Parser;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TagBlock;
use Traversable;

class ForTag extends TagBlock implements HasParseTreeVisitorChildren
{
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

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        $this->forBlock = $this->bodySections[0];

        if (count($this->bodySections) > 1) {
            $this->elseBlock = $this->bodySections[1];
        }

        $this->parseForBlock($parseContext, $this->forBlock->startDelimiter()->markup ?? '');

        if ($this->blank()) {
            $this->forBlock->removeBlankStrings();
            $this->elseBlock?->removeBlankStrings();
        }

        return $this;
    }

    public function render(Context $context): string
    {
        $segment = $this->collectionSegment($context);

        if ($segment === []) {
            return $this->renderElse($context);
        }

        return $this->renderSegment($context, $segment);
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

    protected function parseForBlock(ParseContext $parseContext, string $markup): void
    {
        $parser = new Parser($markup);

        $this->variableName = $parser->consume(TokenType::Identifier);

        if (! $parser->idOrFalse('in')) {
            throw new SyntaxException($parseContext->locale->translate('errors.syntax.for_invalid_in'));
        }

        $collectionNameMarkup = $parser->expression();
        $this->collectionName = $this->parseExpression($parseContext, $collectionNameMarkup);

        $this->name = sprintf('%s-%s', $this->variableName, $collectionNameMarkup);
        $this->reversed = $parser->idOrFalse('reversed') !== false;

        while ($parser->look(TokenType::Comma) || $parser->look(TokenType::Identifier)) {
            $parser->consumeOrFalse(TokenType::Comma);

            $attribute = $parser->idOrFalse('limit') ?: $parser->idOrFalse('offset');

            if (! $attribute) {
                throw new SyntaxException($parseContext->locale->translate('errors.syntax.for_invalid_attribute'));
            }

            $parser->consume(TokenType::Colon);

            $this->setAttribute($parseContext, $attribute, $parser->expression());
        }

        $parser->consume(TokenType::EndOfString);
    }

    protected function setAttribute(ParseContext $parseContext, string $attribute, string $expression): void
    {
        if ($attribute === 'offset') {
            $this->from = $expression === 'continue' ? 'continue' : $this->parseExpression($parseContext, $expression);

            return;
        }

        if ($attribute === 'limit') {
            $this->limit = $this->parseExpression($parseContext, $expression);

            return;
        }
    }

    protected function isSubTag(string $tagName): bool
    {
        return in_array($tagName, ['else'], true);
    }

    protected function collectionSegment(Context $context): array
    {
        $offsets = $context->getRegister('for') ?? [];
        assert(is_array($offsets));

        $collection = $context->evaluate($this->collectionName) ?? [];
        $collection = match (true) {
            $collection instanceof Range => $collection->toArray(),
            $collection instanceof Traversable => iterator_to_array($collection),
            is_iterable($collection) => (array) $collection,
            default => $collection,
        };
        assert(is_array($collection));

        if ($this->from === 'continue') {
            $offset = $offsets[$this->name];
        } else {
            $fromValue = $context->evaluate($this->from);
            $offset = $fromValue === null ? 0 : (is_numeric($fromValue) ? (int) $fromValue : throw new InvalidArgumentException('Invalid integer'));
        }
        assert(is_int($offset));

        $limitValue = $context->evaluate($this->limit);
        $length = $limitValue === null ? null : (is_numeric($limitValue) ? (int) $limitValue : throw new InvalidArgumentException('Invalid integer'));

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
