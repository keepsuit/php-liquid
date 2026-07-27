#!/usr/bin/env php
<?php

declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Usage: php tools/phpbench-compare.php <base.json> <pr.json> [output.md]\n");
    exit(2);
}

[$script, $basePath, $prPath] = $argv;
$outputPath = $argv[3] ?? null;

$baseData = loadJson($basePath);
$prData = loadJson($prPath);

$baseBenchmarks = extractBenchmarks($baseData);
$prBenchmarks = extractBenchmarks($prData);

if ($baseBenchmarks === [] || $prBenchmarks === []) {
    fwrite(STDERR, "Unable to extract comparable benchmarks from JSON output.\n");
    exit(2);
}

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
    if ($base['mean'] != 0.0) {
        $deltaPercent = (($pr['mean'] - $base['mean']) / $base['mean']) * 100;
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
        'baseMean' => $base['mean'],
        'prMean' => $pr['mean'],
        'deltaPercent' => $deltaPercent,
        'baseMemory' => $base['memory'],
        'prMemory' => $pr['memory'],
        'memoryDelta' => $pr['memory'] - $base['memory'],
    ];
}

$averageChange = $percentChanges === []
    ? null
    : array_sum($percentChanges) / count($percentChanges);

$lines = [];
$lines[] = '| Benchmark | Base (mean) | PR (mean) | Δ time | Δ memory |';
$lines[] = '|-----------|-------------|-----------|-------:|---------:|';
foreach ($rows as $row) {
    $lines[] = sprintf(
        '| %s | %s | %s | %s | %s |',
        escapePipe($row['name']),
        formatDuration($row['baseMean']),
        formatDuration($row['prMean']),
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
        $lines[] = '- Missing in PR result: `' . implode('`, `', $missingInPr) . '`';
    }
    if ($missingInBase !== []) {
        sort($missingInBase);
        $lines[] = '- Missing in base result: `' . implode('`, `', $missingInBase) . '`';
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
    $lines[] = sprintf('- Regression threshold (`PHPBENCH_MAX_REG`): **%s**', number_format($thresholdValue, 2) . '%');
    $lines[] = sprintf('- Threshold status: **%s**', $thresholdExceeded ? 'FAILED' : 'PASSED');
}

$markdown = implode("\n", $lines) . "\n";
echo $markdown;

if ($outputPath !== null) {
    file_put_contents($outputPath, $markdown);
}

exit($thresholdExceeded ? 1 : 0);

function loadJson(string $path): array
{
    if (!is_file($path)) {
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

    if (!is_array($decoded)) {
        fwrite(STDERR, "Unexpected JSON structure in {$path}\n");
        exit(2);
    }

    return $decoded;
}

/**
 * @return array<string, array{mean: float, memory: float}>
 */
function extractBenchmarks(array $root): array
{
    $result = [];
    walkNode($root, $result);
    return $result;
}

/**
 * @param array<string, array{mean: float, memory: float}> $result
 */
function walkNode(mixed $node, array &$result): void
{
    if (!is_array($node)) {
        return;
    }

    $name = extractName($node);
    $mean = extractMetric($node, ['mean', 'mean_time', 'time_avg', 'avg', 'mode']);

    if ($name !== null && $mean !== null) {
        $memory = extractMetric($node, ['mem_peak', 'memory_peak', 'memory', 'mem', 'peak_memory']) ?? 0.0;
        $result[$name] = [
            'mean' => $mean,
            'memory' => $memory,
        ];
    }

    foreach ($node as $value) {
        if (is_array($value)) {
            walkNode($value, $result);
        }
    }
}

function extractName(array $node): ?string
{
    $candidates = [];

    if (isset($node['benchmark']) && is_string($node['benchmark'])) {
        $candidates[] = $node['benchmark'];
    }

    if (isset($node['subject']) && is_string($node['subject'])) {
        $candidates[] = $node['subject'];
    }

    if (isset($node['name']) && is_string($node['name'])) {
        $candidates[] = $node['name'];
    }

    if (isset($node['class'], $node['subject']) && is_string($node['class']) && is_string($node['subject'])) {
        $candidates[] = $node['class'] . '::' . $node['subject'];
    }

    foreach ($candidates as $candidate) {
        $value = trim($candidate);
        if ($value !== '') {
            return $value;
        }
    }

    return null;
}

function extractMetric(array $node, array $keys): ?float
{
    foreach ($keys as $key) {
        if (!array_key_exists($key, $node)) {
            continue;
        }

        $value = $node[$key];
        if (is_numeric($value)) {
            return (float) $value;
        }

        if (is_array($value)) {
            foreach ($value as $nestedValue) {
                if (is_numeric($nestedValue)) {
                    return (float) $nestedValue;
                }
            }
        }
    }

    foreach ($node as $value) {
        if (!is_array($value)) {
            continue;
        }

        $nested = extractMetric($value, $keys);
        if ($nested !== null) {
            return $nested;
        }
    }

    return null;
}

function formatDuration(float $microseconds): string
{
    if ($microseconds >= 1_000_000) {
        return number_format($microseconds / 1_000_000, 2) . ' s';
    }

    if ($microseconds >= 1_000) {
        return number_format($microseconds / 1_000, 2) . ' ms';
    }

    return number_format($microseconds, 2) . ' μs';
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
    if ($value === 0.0) {
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
