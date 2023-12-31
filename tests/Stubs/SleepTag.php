<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tag;

class SleepTag extends Tag
{
    protected float $duration;

    public static function tagName(): string
    {
        return 'sleep';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        $this->duration = floatval($this->markup);

        return $this;
    }

    public function render(Context $context): string
    {
        if ($this->duration > 1) {
            sleep((int) $this->duration);
        } else {
            usleep((int) (1_000_000 * $this->duration));
        }

        return '';
    }
}
