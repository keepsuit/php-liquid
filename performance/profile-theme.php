<?php

use Keepsuit\Liquid\Extensions\ProfilerExtension;
use Keepsuit\Liquid\Performance\benchmarks\Support\ComplexThemeFixture;
use Keepsuit\Liquid\Performance\ProfileReport;
use Keepsuit\Liquid\Profiler\Profiler;

require dirname(__DIR__).'/vendor/autoload.php';

$outputPath = null;
/** @var list<string> $arguments */
$arguments = $_SERVER['argv'] ?? [];
for ($argumentIndex = 1; $argumentIndex < count($arguments); $argumentIndex++) {
    $argument = $arguments[$argumentIndex];

    if ($argument === '--help') {
        fwrite(STDOUT, "Usage: php performance/profile-theme.php [--output=profile.json]\n");
        exit(0);
    }

    if ($argument === '--output') {
        $outputPath = $arguments[++$argumentIndex] ?? null;
    } elseif (str_starts_with($argument, '--output=')) {
        $outputPath = substr($argument, strlen('--output='));
    } else {
        fwrite(STDERR, "Unknown argument: {$argument}\n");
        exit(1);
    }

    if (! is_string($outputPath) || $outputPath === '') {
        fwrite(STDERR, "The --output option requires a path.\n");
        exit(1);
    }
}

$environment = ComplexThemeFixture::environment();
$environment->addExtension(new ProfilerExtension(
    profiler: $profiler = new Profiler,
    tags: true,
    variables: true,
));

$template = $environment->parseString(
    source: ComplexThemeFixture::rootTemplateSource(),
    name: ComplexThemeFixture::rootTemplateName(),
);
$template->render(ComplexThemeFixture::newRenderContext($environment));

$report = ProfileReport::fromProfiler($profiler);
$json = json_encode($report, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;

if ($outputPath === null) {
    fwrite(STDOUT, $json);
    exit(0);
}

if (file_put_contents($outputPath, $json) === false) {
    fwrite(STDERR, "Could not write profile report to {$outputPath}.\n");
    exit(1);
}

fwrite(STDOUT, "Profile report written to {$outputPath}\n");
