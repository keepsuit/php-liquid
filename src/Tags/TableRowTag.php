<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Drops\TableRowLoopDrop;
use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Interrupts\BreakInterrupt;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\TagBlock;

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
        try {
            assert($context->body !== null);

            $this->body = $context->body;

            $this->variableName = $context->params->consume(TokenType::Identifier)->data;
            $context->params->id('in');
            $this->collectionName = $context->params->expression();

            while (true) {
                $context->params->consumeOrFalse(TokenType::Comma);

                if ($context->params->isEnd()) {
                    break;
                }

                $attribute = $context->params->consume(TokenType::Identifier)->data;
                $context->params->consume(TokenType::Colon);
                $value = $context->params->expression();
                $this->attributes[$attribute] = $value;
            }

            $context->params->assertEnd();
        } catch (SyntaxException $e) {
            throw SyntaxException::tagSyntaxException(static::tagName(), 'tablerow <var> in <collection> [attributes...]', $e);
        }

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $prepared = self::prepareRows($context, $this->collectionName, $this->attributes);
        if ($prepared === null) {
            return '';
        }
        [$collection, $cols] = $prepared;
        if ($collection === []) {
            // The scope still enforces the configured nesting limit.
            $context->stack(static fn () => null);

            return "<tr class=\"row1\">\n</tr>\n";
        }
        $length = count($collection);

        $output = "<tr class=\"row1\">\n";

        $context->stack(function () use ($collection, $context, $cols, $length, &$output) {
            $tableRowLoop = new TableRowLoopDrop($length, $cols);
            $context->set('tablerowloop', $tableRowLoop);

            foreach ($collection as $item) {
                $context->set($this->variableName, $item);

                $output .= sprintf('<td class="col%s">', $tableRowLoop->col);
                $output .= $this->body->render($context);
                $output .= '</td>';

                $interrupt = $context->popInterrupt();
                if ($interrupt instanceof BreakInterrupt) {
                    break;
                }

                if ($tableRowLoop->col_last && ! $tableRowLoop->last) {
                    $output .= sprintf("</tr>\n<tr class=\"row%s\">", $tableRowLoop->row + 1);
                }

                $tableRowLoop->increment();
            }
        });

        $output .= "</tr>\n";

        return $output;
    }

    /**
     * @internal
     *
     * @param  array<string,mixed>  $attributes
     * @return array{array<mixed>,int}|null
     */
    public static function prepareRows(RenderContext $context, mixed $expression, array $attributes): ?array
    {
        $collection = $context->evaluate($expression);
        if ($collection === null || $collection === false) {
            return null;
        }

        if (! $collection instanceof Range || $collection::class !== Range::class) {
            $collection = Arr::fromCollection($collection);
        }

        $offset = Arr::has($attributes, 'offset') ? ($context->evaluate($attributes['offset']) ?? 0) : 0;
        if (! is_int($offset)) {
            throw new InvalidArgumentException('invalid integer');
        }
        $length = Arr::has($attributes, 'limit') ? ($context->evaluate($attributes['limit']) ?? 0) : null;
        if ($length !== null && ! is_int($length)) {
            throw new InvalidArgumentException('invalid integer');
        }

        $collection = match (true) {
            $collection instanceof Range => $collection->slice($offset, $length),
            $offset === 0 && $length === null => $collection,
            default => array_slice($collection, $offset, $length)
        };
        $cols = Arr::has($attributes, 'cols') ? ($context->evaluate($attributes['cols']) ?? 0) : count($collection);
        if (! is_int($cols)) {
            throw new InvalidArgumentException('invalid integer');
        }

        return [$collection, $cols];
    }

    /** @internal */
    public function compileNative(CompilerContext $context): void
    {
        if ($this->body::class !== BodyNode::class) {
            $context->compileFallback($this);

            return;
        }

        $result = $context->temporaryVariable();
        $context->writeRenderedBlock($this, $result, fn (CompilerContext $context) => $this->compileRows($context));
        $context->writeOutput($result);
    }

    private function compileRows(CompilerContext $context): void
    {
        $prepared = $context->temporaryVariable();
        $output = $context->temporaryVariable();
        $loop = $context->temporaryVariable();
        $item = $context->temporaryVariable();
        $cell = $context->temporaryVariable();
        $context->write($prepared.' = '.$context->writeClassName(self::class).'::prepareRows($context, '
            .$context->writeCachedValue($this->collectionName).', '.$context->writeCachedValue($this->attributes).');');
        $context->write('if ('.$prepared.' !== null) {')->indent();
        $context->write('if ('.$prepared.'[0] === []) {')->indent();
        $context->write('$context->stack(static fn () => null);');
        $context->writeText("<tr class=\"row1\">\n</tr>\n");
        $context->outdent()->write('} else {')->indent();
        $context->write($output.' = '.$context->writeValue("<tr class=\"row1\">\n").';');
        $context->write('$context->stack(function (RenderContext $context) use ('.$prepared.', &'.$output.'): void {')->indent();
        $context->write($loop.' = new '.$context->writeClassName(TableRowLoopDrop::class).'(count('.$prepared.'[0]), '.$prepared.'[1]);');
        $context->write('$context->set("tablerowloop", '.$loop.');');
        $context->write('foreach ('.$prepared.'[0] as '.$item.') {')->indent();
        $context->write('$context->set('.$context->writeValue($this->variableName).', '.$item.');');
        $context->write($output.' .= sprintf('.$context->writeValue('<td class="col%s">').', '.$loop.'->col);');
        $context->writeRenderedBody($this->body, $cell);
        $context->write($output.' .= '.$cell.' . '.$context->writeValue('</td>').';');
        $context->write('if ($context->popInterrupt() instanceof '.$context->writeClassName(BreakInterrupt::class).') {')->indent();
        $context->write('break;')->outdent()->write('}');
        $context->write('if ('.$loop.'->col_last && ! '.$loop.'->last) {')->indent();
        $context->write($output.' .= sprintf('.$context->writeValue("</tr>\n<tr class=\"row%s\">").', '.$loop.'->row + 1);');
        $context->outdent()->write('}');
        $context->write($loop.'->increment();');
        $context->outdent()->write('}');
        $context->outdent()->write('});');
        $context->writeOutput($output.' . '.$context->writeValue("</tr>\n"));
        $context->outdent()->write('}');
        $context->outdent()->write('}');
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
