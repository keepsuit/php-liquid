<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeEvaluated;
use Keepsuit\Liquid\Contracts\CanBeExported;
use Keepsuit\Liquid\Contracts\HasParseTreeVisitorChildren;
use Keepsuit\Liquid\Contracts\IsContextAware;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Support\MissingValue;
use Keepsuit\Liquid\Support\UndefinedVariable;

class VariableLookup implements CanBeEvaluated, CanBeExported, HasParseTreeVisitorChildren
{
    const FILTER_METHODS = ['size', 'first', 'last'];

    private const LOOKUP_REGEX = '{\.([\w\-]+)|\["([\w\-]+)"\]|\[\'([\w\-]+)\'\]|\[(\d+)\]}';

    public function __construct(
        public readonly string $name,
        /** @var array<string|int|VariableLookup> */
        public readonly array $lookups = [],
    ) {}

    /**
     * Parses `a.b[0]["c"]` into a name plus its lookups.
     */
    public static function fromMarkup(string $markup): VariableLookup
    {
        $nameLength = strcspn($markup, '.[');
        $lookupsString = substr($markup, $nameLength);

        if ($lookupsString === '') {
            return new VariableLookup($markup);
        }

        preg_match_all(self::LOOKUP_REGEX, $lookupsString, $matches, PREG_UNMATCHED_AS_NULL);

        // preg_match_all() skips whatever it cannot match, so the matches have to
        // account for the whole string or there was junk between or after them.
        if (implode('', $matches[0]) !== $lookupsString) {
            throw new SyntaxException('Invalid variable lookup: '.$lookupsString);
        }

        $lookups = [];
        foreach (array_keys($matches[0]) as $i) {
            // Every alternative in the pattern captures, so one of them is set.
            $lookup = $matches[1][$i] ?? $matches[2][$i] ?? $matches[3][$i] ?? $matches[4][$i];
            assert($lookup !== null);

            $lookups[] = $lookup;
        }

        return new VariableLookup(substr($markup, 0, $nameLength), $lookups);
    }

    public function export(CompilerContext $context): ?string
    {
        return 'new \\'.self::class.'('
            .$context->writeValue($this->name).', '
            .$context->writeValue($this->lookups).')';
    }

    public function toString(): string
    {
        if ($this->lookups === []) {
            return $this->name;
        }

        return implode('.', [$this->name, ...$this->lookups]);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function parseTreeVisitorChildren(): array
    {
        return [$this->name, ...$this->lookups];
    }

    public function evaluate(RenderContext $context): mixed
    {
        return self::evaluateParts($context, $this->name, $this->lookups);
    }

    /**
     * Evaluate a parsed lookup without requiring a VariableLookup instance.
     *
     * @param  array<string|int|VariableLookup>  $lookups
     */
    public static function evaluateParts(RenderContext $context, string $name, array $lookups): mixed
    {
        $variable = $context->findVariable($name);

        if ($variable instanceof MissingValue) {
            return self::undefinedValue($context, $name, $lookups);
        }

        if ($lookups === []) {
            return $variable;
        }

        $result = self::walkLookupParts($context, $variable, $lookups);

        if (! $result instanceof MissingValue) {
            return $result;
        }

        // The name resolved but the lookup chain broke on the innermost value: an
        // outer scope may still hold one the chain resolves against.
        foreach ($context->findVariables($name) as $candidate) {
            // Skip the value already walked above: re-walking it would repeat any
            // side effects the broken chain triggered on the way.
            if ($candidate === $variable) {
                continue;
            }

            $result = self::walkLookupParts($context, $candidate, $lookups);

            if (! $result instanceof MissingValue) {
                return $result;
            }
        }

        return self::undefinedValue($context, $name, $lookups);
    }

    /**
     * @param  array<string|int|VariableLookup>  $lookups
     */
    private static function undefinedValue(RenderContext $context, string $name, array $lookups): ?UndefinedVariable
    {
        return $context->options->strictVariables
            ? new UndefinedVariable(implode('.', [$name, ...$lookups]))
            : null;
    }

    /**
     * @param  array<string|int|VariableLookup>  $lookups
     */
    private static function walkLookupParts(RenderContext $context, mixed $object, array $lookups): mixed
    {
        if ($object instanceof CanBeEvaluated) {
            $object = $context->evaluate($object);
        }

        if ($object instanceof \Generator) {
            $object = iterator_to_array($object, preserve_keys: false);
        }

        foreach ($lookups as $lookup) {
            $key = $lookup instanceof VariableLookup ? $context->evaluate($lookup) : $lookup;

            if (! (is_string($key) || is_int($key))) {
                return new MissingValue;
            }

            $nextObject = $context->internalContextLookup($object, $key);

            if ($nextObject instanceof CanBeEvaluated) {
                $nextObject = $context->evaluate($nextObject);
            }

            if ($nextObject instanceof MissingValue) {
                if (is_iterable($object) && is_string($lookup) && in_array($lookup, self::FILTER_METHODS, true)) {
                    $nextObject = $context->applyFilter($lookup, $object);
                } else {
                    return $nextObject;
                }
            }

            $object = $nextObject;
            if ($object instanceof IsContextAware) {
                $object->setContext($context);
            }
        }

        return $object;
    }
}
