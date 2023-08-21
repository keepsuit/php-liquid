<?php

namespace Keepsuit\Liquid\Exceptions;

abstract class LiquidException extends \Exception
{
    protected ?int $markupLine = null;

    public function setLineNumber(?int $lineNumber): static
    {
        $this->markupLine = $lineNumber;

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s%s: %s',
            $this->messagePrefix(),
            $this->markupLine ? sprintf(' (line %s)', $this->markupLine) : '',
            $this->getMessage()
        );
    }

    protected function messagePrefix(): string
    {
        return 'Liquid error';
    }
}
