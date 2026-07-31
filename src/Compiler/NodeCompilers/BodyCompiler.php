<?php

namespace Keepsuit\Liquid\Compiler\NodeCompilers;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Compiler\NodeCompilerInterface;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Node;

class BodyCompiler implements NodeCompilerInterface
{
    public function compile(Node $node, CompilerContext $context): ?string
    {
        if (! $node instanceof BodyNode) {
            return null;
        }

        $lines = [
            '\\Keepsuit\\Liquid\\Compiler\\CompiledTemplate::renderCompiledBody(',
            '    $context,',
            '    static function (\\Keepsuit\\Liquid\\Render\\RenderContext $context): string {',
            '        $output = \'\';',
        ];

        foreach ($node->children() as $child) {
            $compiled = $context->compileNode($child);

            if ($compiled === null) {
                return null;
            }

            $lines[] = '        if (! $context->hasInterrupt()) {';
            $lines[] = '            $output .= '.$compiled.';';
            $lines[] = '        }';
        }

        $lines[] = '        return $output;';
        $lines[] = '    },';
        $lines[] = '    '.count($node->children()).',';
        $lines[] = ')';

        return implode("\n", $lines);
    }
}
