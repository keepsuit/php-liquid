<?php

use Keepsuit\Liquid\ErrorMode;
use Keepsuit\Liquid\Template;

beforeEach(function () {
    Template::$errorMode = ErrorMode::Strict;
});

function fixture(string $path): string
{
    return __DIR__.'/fixtures/'.$path;
}
