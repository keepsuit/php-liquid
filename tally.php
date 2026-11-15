<?php

use Tally\Configuration;

// ponytail: only OperationBench while we evaluate Tally; ThemeBench and TemplateCacheBench stay on PHPBench.
return Configuration::create()
    ->withPaths([__DIR__.'/performance/benchmarks/OperationBench.php'])
    ->withIniEntries([
        'opcache.enable=1',
        'opcache.enable_cli=1',
        'opcache.jit=disable',
        // Otherwise opcache skips files modified in the last 2s (e.g. right after `tally accept`) and the counts shift.
        'opcache.file_update_protection=0',
    ]);
