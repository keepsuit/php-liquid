<?php

namespace Keepsuit\Liquid\Contracts;

interface LiquidExtension
{
    /**
     * @return NodeVisitor[]
     */
    public function getNodeVisitors(): array;

    /**
     * @return array<string,mixed>
     */
    public function getRegisters(): array;
}
