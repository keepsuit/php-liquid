<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Nodes\Node;

interface NodeCompilerInterface
{
    public function compile(Node $node, CompilerContext $context): ?string;
}
