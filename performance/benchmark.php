#!/usr/bin/env php
<?php

use Keepsuit\Liquid\Performance\BenchmarkResult;
use Keepsuit\Liquid\Performance\BenchmarkRunner;
use Keepsuit\Liquid\Performance\Shopify\CommentFormTag;
use Keepsuit\Liquid\Performance\Shopify\PaginateTag;
use Keepsuit\Liquid\Performance\ThemeRunner;
use Keepsuit\Liquid\Template;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\SingleCommandApplication;
use Symfony\Component\Console\Style\SymfonyStyle;

require __DIR__.'/../vendor/autoload.php';

Template::registerTag(CommentFormTag::class);
Template::registerTag(PaginateTag::class);

(new SingleCommandApplication())
    ->setName('Benchmark')
    ->setCode(function (InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        $times = 10;
        $warmup = 5;

        $output->writeln(sprintf('Running benchmark for %s seconds (with %s seconds warmup).', $times, $warmup));

        $benchmark = new BenchmarkRunner();
        $profiler = new ThemeRunner();

        if ($warmup > 0) {
            $output->writeln('Warming up...');
            $benchmark->run($warmup, fn () => $profiler->compile());
        }

        $output->writeln('Benchmarking...');
        $computeTable = $style->createTable();
        $computeTable->setHeaders([
            'test',
            'operations/second',
            'error',
            'runs',
            'duration',
        ]);
        outputBenchmarkResult($computeTable, 'parse', $benchmark->run($times, fn () => $profiler->compile()));
        outputBenchmarkResult($computeTable, 'render', $benchmark->run($times, fn () => $profiler->render()));
        outputBenchmarkResult($computeTable, 'parse & render', $benchmark->run($times, fn () => $profiler->run()));
        $computeTable->render();
    })
    ->run();

function outputBenchmarkResult(Table $table, string $testName, BenchmarkResult $result): void
{
    $table->addRow([
        $testName,
        sprintf('%.3f i/s', $result->ops),
        sprintf('(± %.1f%%)', $result->errorPercentage),
        sprintf('%d', $result->runsCount),
        sprintf('%.6f s', $result->durationS()),
    ]);
}
