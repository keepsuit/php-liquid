<?php

/**
 * @return array{benchmark: string, subject: string, set: string, its: int, revs: int, mode: float, mem_peak: int, rstdev: float}
 */
function phpBenchAggregateRow(int $iterations = 10, int $revolutions = 20, float $mode = 1000.0): array
{
    return [
        'benchmark' => 'ThemeBench',
        'subject' => 'benchRender',
        'set' => '',
        'its' => $iterations,
        'revs' => $revolutions,
        'mode' => $mode,
        'mem_peak' => 1_024,
        'rstdev' => 1.0,
    ];
}

/**
 * @param  list<array<string, int|float|string>>  $base
 * @param  list<array<string, int|float|string>>  $pr
 * @return array{exit_code: int, stdout: string, stderr: string}
 */
function runPhpBenchCompare(array $base, array $pr, ?string $threshold = null): array
{
    $directory = sys_get_temp_dir().'/php-liquid-phpbench-'.bin2hex(random_bytes(6));
    mkdir($directory);
    $basePath = $directory.'/base.json';
    $prPath = $directory.'/pr.json';
    file_put_contents($basePath, json_encode($base, JSON_THROW_ON_ERROR));
    file_put_contents($prPath, json_encode($pr, JSON_THROW_ON_ERROR));

    $command = implode(' ', [
        escapeshellarg(PHP_BINARY),
        escapeshellarg(dirname(__DIR__, 3).'/tools/phpbench-compare.php'),
        escapeshellarg($basePath),
        escapeshellarg($prPath),
    ]);
    $environment = $threshold === null
        ? null
        : [...getenv(), 'PHPBENCH_MAX_REG' => $threshold];

    $process = proc_open($command, [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ], $pipes, null, $environment);

    if (! is_resource($process)) {
        throw new RuntimeException('Could not run the PHPBench comparator.');
    }

    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exitCode = proc_close($process);

    unlink($basePath);
    unlink($prPath);
    rmdir($directory);

    return [
        'exit_code' => $exitCode,
        'stdout' => $stdout,
        'stderr' => $stderr,
    ];
}

test('the PHPBench comparator enforces its configured regression threshold', function () {
    $result = runPhpBenchCompare(
        base: [phpBenchAggregateRow(mode: 1000.0)],
        pr: [phpBenchAggregateRow(mode: 1100.0)],
        threshold: '5',
    );

    expect($result['exit_code'])->toBe(1)
        ->and($result['stdout'])->toContain('Threshold status: **FAILED**');
});

test('the PHPBench comparator rejects rows with different sample settings', function () {
    $result = runPhpBenchCompare(
        base: [phpBenchAggregateRow(iterations: 10, revolutions: 20)],
        pr: [phpBenchAggregateRow(iterations: 20, revolutions: 20)],
    );

    expect($result['exit_code'])->toBe(2)
        ->and($result['stdout'])->toContain('Incomparable benchmark rows')
        ->toContain('base: 10 iterations x 20 revs; PR: 20 iterations x 20 revs');
});

test('the PHPBench comparator rejects malformed aggregate JSON', function () {
    $result = runPhpBenchCompare(base: [[]], pr: [phpBenchAggregateRow()]);

    expect($result['exit_code'])->toBe(2)
        ->and($result['stderr'])->toContain('Unexpected PHPBench aggregate JSON');
});

test('the PHPBench comparator reports branch-only subjects without comparing them', function () {
    $branchOnlyRow = phpBenchAggregateRow();
    $branchOnlyRow['benchmark'] = 'CompilerBench';
    $branchOnlyRow['subject'] = 'benchCompiledRender';

    $result = runPhpBenchCompare(
        base: [phpBenchAggregateRow()],
        pr: [$branchOnlyRow],
    );

    expect($result['exit_code'])->toBe(0)
        ->and($result['stdout'])->toContain('No comparable benchmark rows')
        ->toContain('Branch-only subjects (missing in base result): `CompilerBench::benchCompiledRender`');
});
