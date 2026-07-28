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

$rows = [];
$improved = 0;
$regressions = 0;
$worstRegression = null;
$percentChanges = [];

foreach ($sharedNames as $name) {
    $base = $baseBenchmarks[$name];
    $pr = $prBenchmarks[$name];

    $deltaPercent = null;
    if (abs($base['time']) > PHP_FLOAT_EPSILON) {
        $deltaPercent = (($pr['time'] - $base['time']) / $base['time']) * 100;
        $percentChanges[] = $deltaPercent;

        if ($deltaPercent < 0) {
            $improved++;
        } elseif ($deltaPercent > 0) {
            $regressions++;
            if ($worstRegression === null || $deltaPercent > $worstRegression['delta']) {
                $worstRegression = ['name' => $name, 'delta' => $deltaPercent];
            }
        }
    }

    $rows[] = [
        'name' => $name,
        'baseTime' => $base['time'],
        'prTime' => $pr['time'],
        'deltaPercent' => $deltaPercent,
        'baseMemory' => $base['memory'],
        'prMemory' => $pr['memory'],
        'memoryDelta' => $pr['memory'] - $base['memory'],
    ];
}

$changeCount = count($percentChanges);
$averageChange = $changeCount === 0
    ? null
    : array_sum($percentChanges) / $changeCount;

$lines = [];
$lines[] = '| Benchmark | Base (mode) | PR (mode) | Delta time | Delta memory |';
$lines[] = '|-----------|-------------|-----------|-------:|---------:|';
foreach ($rows as $row) {
    $lines[] = sprintf(
        '| %s | %s | %s | %s | %s |',
        escapePipe($row['name']),
        formatDuration($row['baseTime']),
        formatDuration($row['prTime']),
        formatPercent($row['deltaPercent']),
        formatBytesSigned($row['memoryDelta'])
    );
}

$lines[] = '';
$lines[] = sprintf('- Improved benchmarks: **%d**', $improved);
$lines[] = sprintf('- Regressions: **%d**', $regressions);
$lines[] = sprintf(
    '- Worst regression: **%s**',
    $worstRegression === null
        ? 'n/a'
        : sprintf('%s (%s)', $worstRegression['name'], formatPercent($worstRegression['delta']))
);
$lines[] = sprintf('- Average change: **%s**', formatPercent($averageChange));

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

    $worst = $worstRegression['delta'] ?? 0.0;
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
 * @return array<string, array{time: float, memory: float}>
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
            || ! is_numeric($row['mode'] ?? null)
            || ! is_numeric($row['mem_peak'] ?? null)) {
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
        ];
    }

    if ($benchmarks === []) {
        fwrite(STDERR, "No benchmarks found in {$path}\n");
        exit(2);
    }

    return $benchmarks;
}

function formatDuration(float $microseconds): string
{
    if ($microseconds >= 1_000_000) {
        return number_format($microseconds / 1_000_000, 2).' s';
    }

    if ($microseconds >= 1_000) {
        return number_format($microseconds / 1_000, 2).' ms';
    }

    return number_format($microseconds, 2).' us';
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
