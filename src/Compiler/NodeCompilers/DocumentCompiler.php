<?php

namespace Keepsuit\Liquid\Compiler\NodeCompilers;

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Compiler\NodeCompilerInterface;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Node;

class DocumentCompiler implements NodeCompilerInterface
{
    public function compile(Node $node, CompilerContext $context): ?string
    {
        return $node instanceof Document ? $context->compileNode($node->body) : null;
    }
}
