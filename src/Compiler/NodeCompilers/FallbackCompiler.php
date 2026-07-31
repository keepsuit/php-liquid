<?php

namespace Keepsuit\Liquid\Compiler\NodeCompilers;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Compiler\NodeCompilerInterface;
use Keepsuit\Liquid\Nodes\Node;

class FallbackCompiler implements NodeCompilerInterface
{
    public function compile(Node $node, CompilerContext $context): ?string
    {
        $nodeCode = $context->exportSerializedValue($node);
        $lineNumber = $context->exportValue($node->lineNumber());

        if ($nodeCode === null || $lineNumber === null) {
            return null;
        }

        return '\\Keepsuit\\Liquid\\Compiler\\CompiledTemplate::renderNode('
            .'$context, '.$nodeCode.', '.$lineNumber.')';
    }
}
