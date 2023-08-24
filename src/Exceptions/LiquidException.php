<?php

namespace Keepsuit\Liquid\Exceptions;

abstract class LiquidException extends \Exception
{
    public ?int $lineNumber = null;

    public ?string $templateName = null;

    public ?string $markupContext = null;

    public function render(): string
    {
        return sprintf(
            '%s%s: %s%s',
            $this->messagePrefix(),
            $this->lineNumber ? sprintf(' (%sline %s)', $this->templateName ? ($this->templateName.' ') : '', $this->lineNumber) : '',
            $this->getMessage(),
            $this->markupContext ? sprintf(' %s', $this->markupContext) : ''
        );
    }

    public function __toString(): string
    {
        return $this->render();
    }

    protected function messagePrefix(): string
    {
        return 'Liquid error';
    }
}
