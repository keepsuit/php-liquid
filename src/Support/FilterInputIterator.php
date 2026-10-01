<?php

namespace Keepsuit\Liquid\Support;

use IteratorAggregate;
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
        protected FilterSupport $support,
    ) {}

    public function getIterator(): Traversable
    {
        if ($this->input instanceof UndefinedVariable) {
            throw $this->input->toException();
        }

        if (is_array($this->input) && array_is_list($this->input)) {
            return $this->flatten($this->input);
        }

        $input = match (true) {
            $this->input === null => [],
            $this->input instanceof Traversable => $this->input,
            default => [$this->input],
        };

        return $this->normalize($input);
    }

    protected function normalize(iterable $input): Traversable
    {
        foreach ($input as $value) {
            yield $this->support->normalize($value);
        }
    }

    protected function flatten(array $input): Traversable
    {
        foreach ($input as $value) {
            if (is_array($value) && array_is_list($value)) {
                foreach ($this->flatten($value) as $item) {
                    yield $item;
                }
            } else {
                yield $this->support->normalize($value);
            }
        }
    }
}
