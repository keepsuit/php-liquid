#!/usr/bin/env php
<?php

declare(strict_types=1);

if ($argc !== 3) {
    fwrite(STDERR, "Usage: php tools/phpbench-compare.php <base.json> <pr.json>\n");
    exit(2);
}

[, $basePath, $prPath] = $argv;

$baseBenchmarks = loadBenchmarks($basePath);
$prBenchmarks = loadBenchmarks($prPath);

$sharedNames = array_values(array_intersect(array_keys($baseBenchmarks), array_keys($prBenchmarks)));
sort($sharedNames);

if ($sharedNames === []) {
    fwrite(STDERR, "No common benchmark names found between base and PR results.\n");
    exit(2);
}

// Runner jitter is routinely ±2%, so a single median over every benchmark is
// meaningless when the set is bimodal (big winners + flat benchmarks): the middle
// element lands on whichever side happens to hold more rows. Bucket instead, and
// report a median per bucket.
const NOISE_THRESHOLD_PERCENT = 2.0;
const NOISE_THRESHOLD_LABEL = '2';

$rows = [];
$worstRegression = null;
$improvedChanges = [];
$neutralChanges = [];
$regressedChanges = [];
$totalBaseMemory = 0.0;
$totalPrMemory = 0.0;

foreach ($sharedNames as $name) {
    $base = $baseBenchmarks[$name];
    $pr = $prBenchmarks[$name];

    $deltaPercent = null;
    $baseOpsPerSecond = null;
    $prOpsPerSecond = null;
    if (abs($base['time']) > PHP_FLOAT_EPSILON && abs($pr['time']) > PHP_FLOAT_EPSILON) {
        $baseOpsPerSecond = 1_000_000 / $base['time'];
        $prOpsPerSecond = 1_000_000 / $pr['time'];
        $deltaPercent = (($prOpsPerSecond - $baseOpsPerSecond) / $baseOpsPerSecond) * 100;

        if ($deltaPercent > NOISE_THRESHOLD_PERCENT) {
            $improvedChanges[] = $deltaPercent;
        } elseif ($deltaPercent < -NOISE_THRESHOLD_PERCENT) {
            $regressedChanges[] = $deltaPercent;
            if ($worstRegression === null || $deltaPercent < $worstRegression['delta']) {
                $worstRegression = ['name' => $name, 'delta' => $deltaPercent];
            }
        } else {
            $neutralChanges[] = $deltaPercent;
        }
    }

    $memoryDeltaPercent = null;
    if (abs($base['memory']) > PHP_FLOAT_EPSILON) {
        $memoryDeltaPercent = (($pr['memory'] - $base['memory']) / $base['memory']) * 100;
        $totalBaseMemory += $base['memory'];
        $totalPrMemory += $pr['memory'];
    }

    $rows[] = [
        'name' => $name,
        'baseOpsPerSecond' => $baseOpsPerSecond,
        'prOpsPerSecond' => $prOpsPerSecond,
        'deltaPercent' => $deltaPercent,
        'baseRstdev' => $base['rstdev'],
        'prRstdev' => $pr['rstdev'],
        'baseMemory' => $base['memory'],
        'prMemory' => $pr['memory'],
        'memoryDelta' => $pr['memory'] - $base['memory'],
        'memoryDeltaPercent' => $memoryDeltaPercent,
    ];
}

// Weighted by actual bytes, so benchmarks that barely allocate cannot outvote the
// ones that allocate megabytes (a plain mean of per-benchmark percentages did).
$totalMemoryChange = abs($totalBaseMemory) > PHP_FLOAT_EPSILON
    ? (($totalPrMemory - $totalBaseMemory) / $totalBaseMemory) * 100
    : null;

$lines = [];
$context = benchmarkContext($baseBenchmarks[$sharedNames[0]]);
if ($context !== null) {
    $lines[] = $context;
    $lines[] = '';
}
$lines[] = '> Positive ops/s is faster. RSD above 5% is marked high.';
$lines[] = '';
$lines[] = '| Benchmark | Base ops/s | PR ops/s | Delta ops/s | RSD (base / PR) | Delta memory | Memory % |';
$lines[] = '|-----------|-----------:|---------:|------------:|----------------:|-------------:|---------:|';
foreach ($rows as $row) {
    $lines[] = sprintf(
        '| %s | %s | %s | %s | %s | %s | %s |',
        escapePipe($row['name']),
        formatOperationsPerSecond($row['baseOpsPerSecond']),
        formatOperationsPerSecond($row['prOpsPerSecond']),
        formatPercent($row['deltaPercent']),
        formatRstdev($row['baseRstdev'], $row['prRstdev']),
        formatBytesSigned($row['memoryDelta']),
        formatPercent($row['memoryDeltaPercent'])
    );
}

$lines[] = '';
$lines[] = sprintf(
    '- Improved (> +%s%%): **%d**%s',
    NOISE_THRESHOLD_LABEL,
    count($improvedChanges),
    $improvedChanges === [] ? '' : sprintf(' (median %s)', formatPercent(median($improvedChanges)))
);
$lines[] = sprintf(
    '- Neutral (within ±%s%%): **%d**',
    NOISE_THRESHOLD_LABEL,
    count($neutralChanges)
);
$lines[] = sprintf(
    '- Regressed (< -%s%%): **%d**%s',
    NOISE_THRESHOLD_LABEL,
    count($regressedChanges),
    $regressedChanges === [] ? '' : sprintf(' (median %s)', formatPercent(median($regressedChanges)))
);
$lines[] = sprintf(
    '- Worst throughput regression: **%s**',
    $worstRegression === null
        ? 'n/a'
        : sprintf('%s (%s)', $worstRegression['name'], formatPercent($worstRegression['delta']))
);
$lines[] = sprintf('- Total memory change: **%s**', formatPercent($totalMemoryChange));

$missingInPr = array_values(array_diff(array_keys($baseBenchmarks), array_keys($prBenchmarks)));
$missingInBase = array_values(array_diff(array_keys($prBenchmarks), array_keys($baseBenchmarks)));
if ($missingInPr !== [] || $missingInBase !== []) {
    $lines[] = '';
    if ($missingInPr !== []) {
        sort($missingInPr);
        $lines[] = '- Missing in PR result: `'.implode('`, `', $missingInPr).'`';
    }
    if ($missingInBase !== []) {
        sort($missingInBase);
        $lines[] = '- Missing in base result: `'.implode('`, `', $missingInBase).'`';
    }
}

$threshold = getenv('PHPBENCH_MAX_REG');
$thresholdExceeded = false;
if ($threshold !== false && $threshold !== '') {
    $thresholdValue = filter_var($threshold, FILTER_VALIDATE_FLOAT);
    if ($thresholdValue === false) {
        fwrite(STDERR, "Invalid PHPBENCH_MAX_REG value: {$threshold}\n");
        exit(2);
    }

    $worst = abs($worstRegression['delta'] ?? 0.0);
    if ($worst > $thresholdValue) {
        $thresholdExceeded = true;
    }

    $lines[] = '';
    $lines[] = sprintf('- Regression threshold (`PHPBENCH_MAX_REG`): **%s**', number_format($thresholdValue, 2).'%');
    $lines[] = sprintf('- Threshold status: **%s**', $thresholdExceeded ? 'FAILED' : 'PASSED');
}

$markdown = implode("\n", $lines)."\n";
echo $markdown;

exit($thresholdExceeded ? 1 : 0);

/**
 * @return array<string, array{time: float, memory: float, rstdev: float, iterations: int, revolutions: int}>
 */
function loadBenchmarks(string $path): array
{
    if (! is_file($path)) {
        fwrite(STDERR, "File not found: {$path}\n");
        exit(2);
    }

    $content = file_get_contents($path);
    if ($content === false) {
        fwrite(STDERR, "Unable to read file: {$path}\n");
        exit(2);
    }

    try {
        $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        fwrite(STDERR, "Invalid JSON in {$path}: {$e->getMessage()}\n");
        exit(2);
    }

    if (! is_array($decoded) || ! array_is_list($decoded)) {
        fwrite(STDERR, "Unexpected JSON structure in {$path}\n");
        exit(2);
    }

    $benchmarks = [];
    foreach ($decoded as $row) {
        if (! is_array($row)
            || ! is_string($row['benchmark'] ?? null)
            || ! is_string($row['subject'] ?? null)
            || ! is_string($row['set'] ?? '')
            || ! is_numeric($row['its'] ?? null)
            || ! is_numeric($row['revs'] ?? null)
            || ! is_numeric($row['mode'] ?? null)
            || ! is_numeric($row['mem_peak'] ?? null)
            || ! is_numeric($row['rstdev'] ?? null)) {
            fwrite(STDERR, "Unexpected PHPBench aggregate JSON in {$path}\n");
            exit(2);
        }

        $name = $row['benchmark'].'::'.$row['subject'];
        if ($row['set'] !== '') {
            $name .= ' ('.$row['set'].')';
        }

        $benchmarks[$name] = [
            'time' => (float) $row['mode'],
            'memory' => (float) $row['mem_peak'],
            'rstdev' => (float) $row['rstdev'],
            'iterations' => (int) $row['its'],
            'revolutions' => (int) $row['revs'],
        ];
    }

    if ($benchmarks === []) {
        fwrite(STDERR, "No benchmarks found in {$path}\n");
        exit(2);
    }

    return $benchmarks;
}

function formatOperationsPerSecond(?float $operationsPerSecond): string
{
    if ($operationsPerSecond === null) {
        return 'n/a';
    }

    return number_format($operationsPerSecond, 2).' ops/s';
}

function formatRstdev(float $baseRstdev, float $prRstdev): string
{
    $value = sprintf('%.2f%% / %.2f%%', $baseRstdev, $prRstdev);

    return $baseRstdev > 5 || $prRstdev > 5 ? $value.' (high)' : $value;
}

/**
 * @param  array{iterations: int, revolutions: int}  $benchmark
 */
function benchmarkContext(array $benchmark): ?string
{
    $phpVersion = getenv('PHPBENCH_PHP_VERSION');
    $runner = getenv('PHPBENCH_RUNNER');
    $baseSha = getenv('PHPBENCH_BASE_SHA');
    $prSha = getenv('PHPBENCH_PR_SHA');
    $warmup = getenv('PHPBENCH_WARMUP');

    if ($phpVersion === false || $runner === false || $baseSha === false || $prSha === false || $warmup === false) {
        return null;
    }

    return sprintf(
        'PHP %s | Runner %s | Base `%s` | PR `%s` | %d iterations x %d revs | %s warmup',
        $phpVersion,
        $runner,
        substr($baseSha, 0, 7),
        substr($prSha, 0, 7),
        $benchmark['iterations'],
        $benchmark['revolutions'],
        $warmup,
    );
}

/**
 * @param  list<float>  $values
 */
function median(array $values): ?float
{
    if ($values === []) {
        return null;
    }

    sort($values, SORT_NUMERIC);
    $middle = intdiv(count($values), 2);

    return count($values) % 2 === 0
        ? ($values[$middle - 1] + $values[$middle]) / 2
        : $values[$middle];
}

function formatPercent(?float $value): string
{
    if ($value === null) {
        return 'n/a';
    }

    return sprintf('%+.2f%%', $value);
}

function formatBytesSigned(float $value): string
{
    if (abs($value) < PHP_FLOAT_EPSILON) {
        return '0 B';
    }

    $sign = $value > 0 ? '+' : '-';
    $absolute = abs($value);
    $units = ['B', 'KiB', 'MiB', 'GiB'];
    $unitIndex = 0;
    while ($absolute >= 1024 && $unitIndex < count($units) - 1) {
        $absolute /= 1024;
        $unitIndex++;
    }

    return sprintf('%s%.2f %s', $sign, $absolute, $units[$unitIndex]);
}

function escapePipe(string $value): string
{
    return str_replace('|', '\|', $value);
}
