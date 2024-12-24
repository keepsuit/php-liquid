<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Drops\TableRowLoopDrop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Interrupts\BreakInterrupt;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TagBlock;
use Traversable;

class TableRowTag extends TagBlock
{
    protected string $variableName;

    protected mixed $collectionName;

    protected array $attributes = [];

    protected BodyNode $body;

    public static function tagName(): string
    {
        return 'tablerow';
    }

    public function parse(TagParseContext $context): static
    {
        assert($context->body !== null);

        $this->body = $context->body;

        $this->variableName = $context->params->consume(TokenType::Identifier)->data;
        $context->params->id('in');
        $this->collectionName = $context->params->expression();

        while ($context->params->look(TokenType::Identifier)) {
            $attribute = $context->params->consume(TokenType::Identifier)->data;
            $context->params->consume(TokenType::Colon);
            $value = $context->params->expression();
            $this->attributes[$attribute] = $value;
        }

        $context->params->assertEnd();

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $collection = $context->evaluate($this->collectionName) ?? [];
        $collection = match (true) {
            $collection instanceof Range => $collection->toArray(),
            $collection instanceof Traversable => iterator_to_array($collection),
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

                $output .= sprintf('%s<td class="col%s">', PHP_EOL, $tableRowLoop->col);
                $output .= $this->body->render($context);
                $output .= '</td>';

                $interrupt = $context->popInterrupt();
                if ($interrupt instanceof BreakInterrupt) {
                    break;
                }

                if ($tableRowLoop->col_last && ! $tableRowLoop->last) {
                    $output .= sprintf('%s</tr>%s<tr class="row%s">', PHP_EOL, PHP_EOL, $tableRowLoop->row + 1);
                }

                $tableRowLoop->increment();
            }
        });

        $output .= PHP_EOL.'</tr>';

        return $output;
    }

    public function parseTreeVisitorChildren(): array
    {
        return Arr::compact([
            $this->body,
            ...$this->attributes,
            $this->collectionName,
        ]);
    }
}
