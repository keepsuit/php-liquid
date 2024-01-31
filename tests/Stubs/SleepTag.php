<?php

namespace Keepsuit\Liquid\Tests\Stubs;

use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;

class SleepTag extends Tag
{
    protected VariableLookup|float $duration;

    public static function tagName(): string
    {
        return 'sleep';
    }

    public function parse(TagParseContext $context): static
    {
        $duration = $context->params->expression();
        $this->duration = match (true) {
            is_numeric($duration) => floatval($duration),
            $duration instanceof VariableLookup => $duration,
            default => throw new InvalidArgumentException('Invalid duration value'),
        };

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $duration = match (true) {
            is_numeric($this->duration) => $this->duration,
            $this->duration instanceof VariableLookup => $this->duration->evaluate($context),
        };
        assert(is_numeric($duration));

        if ($duration > 1) {
            sleep((int) $duration);
        } else {
            usleep((int) (1_000_000 * $duration));
        }

        return '';
    }
}
