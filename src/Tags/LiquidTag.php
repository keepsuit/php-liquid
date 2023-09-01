<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Nodes\BlockBodySection;
use Keepsuit\Liquid\Parse\BlockParser;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tag;

class LiquidTag extends Tag
{
    /**
     * @var BlockBodySection[]
     */
    protected array $bodySections;

    public static function tagName(): string
    {
        return 'liquid';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        $liquidTokenizer = $parseContext->newTokenizer(
            markup: $this->markup,
            forLiquidTag: true
        );

        $this->bodySections = BlockParser::forDocument()->parse($liquidTokenizer, $parseContext);

        return $this;
    }

    public function render(Context $context): string
    {
        $output = '';

        foreach ($this->bodySections as $bodySection) {
            $output .= $bodySection->render($context);
        }

        return $output;
    }
}
