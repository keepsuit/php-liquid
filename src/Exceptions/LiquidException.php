<?php

namespace Keepsuit\Liquid\Exceptions;

abstract class LiquidException extends \Exception
{
    public ?int $lineNumber = null;

    public ?string $templateName = null;

    public function __toString(): string
    {
        return sprintf(
            '%s%s: %s',
            $this->messagePrefix(),
            $this->lineNumber ? sprintf(' (%sline %s)', $this->templateName ? ($this->templateName.' ') : '', $this->lineNumber) : '',
            $this->getMessage()
        );
    }

    protected function messagePrefix(): string
    {
        return 'Liquid error';
    }
}
