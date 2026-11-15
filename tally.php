<?php

use Tally\Configuration;

// ponytail: TemplateCacheBench stays on PHPBench, instruction counts miss its file I/O.
return Configuration::create()
    ->withPaths([
        __DIR__.'/performance/benchmarks/OperationBench.php',
        __DIR__.'/performance/benchmarks/ThemeBench.php',
        __DIR__.'/performance/tally',
    ])
    ->withIniEntries([
        'opcache.enable=1',
        'opcache.enable_cli=1',
        'opcache.jit=disable',
        // Otherwise opcache skips files modified in the last 2s (e.g. right after `tally accept`) and the counts shift.
        'opcache.file_update_protection=0',
    ]);
