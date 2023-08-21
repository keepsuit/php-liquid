<?php

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('exception extends LiquidException')
    ->expect('Keepsuit\Liquid\Exceptions')
    ->toExtend(\Keepsuit\Liquid\Exceptions\LiquidException::class);
