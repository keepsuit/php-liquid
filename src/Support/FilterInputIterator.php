<?php

namespace Keepsuit\Liquid\Support;

use IteratorAggregate;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Render\RenderContext;
use Traversable;

/**
 * Normalizes standard array-filter input without flattening hashes or iterators.
 *
 * @implements IteratorAggregate<int, mixed>
 *
 * @internal
 */
class FilterInputIterator implements IteratorAggregate
{
    public function __construct(
        protected mixed $input,
        protected RenderContext $context,
    ) {}

    public function getIterator(): Traversable
    {
        if ($this->input instanceof UndefinedVariable) {
            throw $this->input->toException();
        }

        $input = match (true) {
            $this->input === null => [],
            is_array($this->input) && array_is_list($this->input) => $this->flatten($this->input),
            $this->input instanceof Traversable => $this->input,
            default => [$this->input],
        };

        foreach ($input as $value) {
            $value = $this->context->normalizeValue($value);

            if ($value instanceof IsContextAware) {
                $value->setContext($this->context);
            }

            yield $value;
        }
    }

    protected function flatten(array $input): Traversable
    {
        foreach ($input as $value) {
            if (is_array($value) && array_is_list($value)) {
                yield from $this->flatten($value);
            } else {
                yield $value;
            }
        }
    }
}
