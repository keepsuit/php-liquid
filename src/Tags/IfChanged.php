<?php

namespace Keepsuit\Liquid\Tags;

use Keepsuit\Liquid\Context;
use Keepsuit\Liquid\TagBlock;

class IfChanged extends TagBlock
{
    public static function tagName(): string
    {
        return 'ifchanged';
    }

    public function render(Context $context): string
    {
        $output = parent::render($context);

        if ($context->getRegister('ifchanged') === $output) {
            return '';
        }

        $context->setRegister('ifchanged', $output);

        return $output;
    }
}
