<?php

namespace Keepsuit\Liquid\Compiler\NodeCompilers;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Compiler\NodeCompilerInterface;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\Variable;

class VariableCompiler implements NodeCompilerInterface
{
    public function compile(Node $node, CompilerContext $context): ?string
    {
        if (! $node instanceof Variable) {
            return null;
        }

        $name = $context->exportValue($node->name);
        $filters = $context->exportValue($node->filters);
        $lineNumber = $context->exportValue($node->lineNumber());

        if ($name === null || $filters === null || $lineNumber === null) {
            return null;
        }

        return '\\Keepsuit\\Liquid\\Compiler\\CompiledTemplate::renderVariable('
            .'$context, '.$name.', '.$filters.', '.$lineNumber.')';
    }
}
