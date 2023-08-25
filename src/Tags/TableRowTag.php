<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Drops\TableRowLoopDrop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parser\Regex;
use Keepsuit\Liquid\Parser\Tokenizer;
use Keepsuit\Liquid\Range;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TagBlock;

class TableRowTag extends TagBlock implements HasParseTreeVisitorChildren
{
    const Syntax = '/(\w+)\s+in\s+('.Regex::QuotedFragment.'+)/';

    protected string $variableName;

    protected mixed $collectionName;

    protected array $attributes = [];

    public static function tagName(): string
    {
        return 'tablerow';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        if (! preg_match(self::Syntax, $this->markup, $matches)) {
            throw new SyntaxException($this->parseContext->locale->translate('errors.syntax.table_row'));
        }

        $this->variableName = $matches[1];
        $this->collectionName = $this->parseExpression($matches[2]);

        preg_match_all(sprintf('/%s/', Regex::TagAttributes), $this->markup, $attributeMatches, PREG_SET_ORDER);

        foreach ($attributeMatches as $matches) {
            $this->attributes[$matches[1]] = $this->parseExpression($matches[2]);
        }

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
