<?php

namespace Keepsuit\Liquid\Tests\Stubs;

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

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        $this->duration = floatval($this->markup);

        return $this;
    }

    public function render(Context $context): string
    {
        if ($this->duration > 1) {
            sleep($this->duration);
        } else {
            usleep((int) (1_000_000 * $this->duration));
        }

        return '';
    }
}
