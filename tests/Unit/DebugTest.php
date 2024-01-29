<?php

use Keepsuit\Liquid\Performance\Shopify\CommentFormTag;
use Keepsuit\Liquid\Performance\Shopify\CustomFilters;
use Keepsuit\Liquid\Performance\Shopify\PaginateTag;
use Keepsuit\Liquid\Performance\ThemeRunner;
use Keepsuit\Liquid\TemplateFactory;

test('wip', function () {
    ray()->clearScreen();
    $templateFactory = TemplateFactory::new()
        ->registerTag(CommentFormTag::class)
        ->registerTag(PaginateTag::class)
        ->registerFilter(CustomFilters::class);

    $themeRunner = new ThemeRunner($templateFactory);

    $themeRunner->compile();
});
