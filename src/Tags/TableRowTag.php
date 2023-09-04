<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Drops\TableRowLoopDrop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TagBlock;

class TableRowTag extends TagBlock implements HasParseTreeVisitorChildren
{
    protected string $variableName;

    protected mixed $collectionName;

    protected array $attributes = [];

    public static function tagName(): string
    {
        return 'tablerow';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        $parser = $this->newParser();

        $this->variableName = $parser->consume(TokenType::Identifier);
        $parser->id('in');
        $this->collectionName = $this->parseExpression($parser->expression());

        $this->attributes = array_map(
            fn (string $expression) => $this->parseExpression($expression),
            $parser->attributes()
        );

        $parser->consume(TokenType::EndOfString);

        return $this;
    }

    public function render(Context $context): string
    {
        $collection = $context->evaluate($this->collectionName) ?? [];
        $collection = match (true) {
            $collection instanceof Range => $collection->toArray(),
            $collection instanceof \Iterator => iterator_to_array($collection),
            is_string($collection) => $collection === '' ? [] : str_split($collection),
            default => $collection
        };
        assert(is_array($collection));

        $offset = Arr::has($this->attributes, 'offset') ? ($context->evaluate($this->attributes['offset']) ?? 0) : 0;
        if (! is_int($offset)) {
            throw new InvalidArgumentException('invalid integer');
        }
        $length = Arr::has($this->attributes, 'limit') ? ($context->evaluate($this->attributes['limit']) ?? 0) : null;
        if ($length !== null && ! is_int($length)) {
            throw new InvalidArgumentException('invalid integer');
        }

        $collection = array_slice($collection, $offset, $length);
        $length = count($collection);

        $cols = Arr::has($this->attributes, 'cols') ? ($context->evaluate($this->attributes['cols']) ?? 0) : count($collection);
        if (! is_int($cols)) {
            throw new InvalidArgumentException('invalid integer');
        }

        $output = '<tr class="row1">';

        $context->stack(function () use ($collection, $context, $cols, $length, &$output) {
            $tableRowLoop = new TableRowLoopDrop($length, $cols);
            $context->set('tablerowloop', $tableRowLoop);

            foreach ($collection as $item) {
                $context->set($this->variableName, $item);

                $output .= sprintf('<td class="col%s">', $tableRowLoop->col);
                $output .= parent::render($context);
                $output .= '</td>';

                if ($tableRowLoop->col_last && ! $tableRowLoop->last) {
                    $output .= sprintf('</tr><tr class="row%s">', $tableRowLoop->row + 1);
                }

                $tableRowLoop->increment();
            }
        });

        $output .= '</tr>';

        return $output;
    }

    public function parseTreeVisitorChildren(): array
    {
        return [
            ...$this->nodeList(),
            ...$this->attributes,
            $this->collectionName,
        ];
    }
}
