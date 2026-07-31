<?php

namespace Keepsuit\Liquid\Compiler\NodeCompilers;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Compiler\NodeCompilerInterface;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Nodes\Text;

class TextCompiler implements NodeCompilerInterface
{
    public function compile(Node $node, CompilerContext $context): ?string
    {
        if (! $node instanceof Text && ! $node instanceof Raw) {
            return null;
        }

        return $context->exportValue($node->value);
    }
}
