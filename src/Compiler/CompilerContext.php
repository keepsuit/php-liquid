<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Nodes\Node;

final class CompilerContext
{
    /**
     * @var list<NodeCompilerInterface>
     */
    private array $nodeCompilers;

    /**
     * @param  iterable<NodeCompilerInterface>|null  $nodeCompilers
     */
    public function __construct(?iterable $nodeCompilers = null)
    {
        $this->nodeCompilers = $nodeCompilers === null
            ? [
                new NodeCompilers\DocumentCompiler,
                new NodeCompilers\BodyCompiler,
                new NodeCompilers\TextCompiler,
                new NodeCompilers\VariableCompiler,
                new NodeCompilers\FallbackCompiler,
            ]
            : array_values([...$nodeCompilers]);
    }

    public function compileNode(Node $node): ?string
    {
        if ($node instanceof CanBeCompiled) {
            try {
                $compiled = $node->compile($this);
            } catch (\Throwable) {
                $compiled = null;
            }

            if ($compiled !== null) {
                return $compiled;
            }
        }

        foreach ($this->nodeCompilers as $nodeCompiler) {
            $compiled = $nodeCompiler->compile($node, $this);

            if ($compiled !== null) {
                return $compiled;
            }
        }

        return null;
    }

    /**
     * Export a value as PHP data. Scalars and scalar arrays stay readable in
     * the artifact; other values use a serialized data payload.
     */
    public function exportValue(mixed $value): ?string
    {
        if (is_null($value) || is_bool($value) || is_int($value) || is_string($value)) {
            return var_export($value, true);
        }

        if (is_float($value) && is_finite($value)) {
            return var_export($value, true);
        }

        if (is_array($value)) {
            $parts = [];

            foreach ($value as $key => $item) {
                $keyCode = $this->exportValue($key);
                $itemCode = $this->exportValue($item);

                if ($keyCode === null || $itemCode === null) {
                    break;
                }

                $parts[] = $keyCode.' => '.$itemCode;
            }

            if (count($parts) === count($value)) {
                return '['.implode(', ', $parts).']';
            }
        }

        return $this->exportSerializedValue($value);
    }

    public function exportSerializedValue(mixed $value): ?string
    {
        try {
            $serialized = serialize($value);
        } catch (\Throwable) {
            return null;
        }

        return '\\Keepsuit\\Liquid\\Compiler\\CompiledTemplate::decodeValue('
            .var_export(base64_encode($serialized), true).')';
    }
}
