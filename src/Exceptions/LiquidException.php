<?php

namespace Keepsuit\Liquid\Exceptions;

abstract class LiquidException extends \ErrorException
{
    public ?int $lineNumber = null;

    public ?string $templateName = null;

    public ?string $markupContext = null;

    public function setFile(string $file): void
    {
        $this->file = $file;
    }

    public function setLine(int $line): void
    {
        $this->line = $line;
    }

    public function toLiquidErrorMessage(): string
    {
        return sprintf(
            '%s%s: %s%s',
            $this->messagePrefix(),
            $this->lineNumber ? sprintf(' (%sline %s)', $this->templateName ? ($this->templateName.' ') : '', $this->lineNumber) : '',
            $this->getMessage(),
            $this->markupContext ? sprintf(' in "%s"', trim($this->markupContext)) : ''
        );
    }

    protected function messagePrefix(): string
    {
        return 'Liquid error';
    }
}
