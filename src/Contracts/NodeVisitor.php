<?php

namespace Keepsuit\Liquid\Contracts;

use Keepsuit\Liquid\Nodes\Node;

interface NodeVisitor
{
    public function enterNode(Node $node): void;

    public function leaveNode(Node $node): void;
}
