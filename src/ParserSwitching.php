<?php

namespace Keepsuit\Liquid;

trait ParserSwitching
{
    abstract protected function strictParse(string $markup): mixed;

    abstract protected function laxParse(string $markup): mixed;

    protected function strictParseWithErrorModeFallback(string $markup, ParseContext $parseContext): mixed
    {
        try {
            return $this->strictParseWithErrorContext($markup);
        } catch (SyntaxException $e) {
            if ($parseContext->errorMode === ErrorMode::Strict) {
                throw $e;
            }
            if ($parseContext->errorMode === ErrorMode::Warn) {
                $parseContext->logWarning($e);
            }

            return $this->laxParse($markup);
        }
    }

    private function strictParseWithErrorContext(string $markup): mixed
    {
        try {
            return $this->strictParse($markup);
        } catch (SyntaxException $e) {
            $e->setLineNumber($this->lineNumber);
            throw $e;
        }
    }
}
