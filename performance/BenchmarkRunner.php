<?php

namespace Keepsuit\Liquid\Performance;

class BenchmarkRunner
{
    public function run(int $seconds, \Closure $callback): BenchmarkResult
    {
        gc_collect_cycles();
        if (function_exists('memory_reset_peak_usage')) {
            memory_reset_peak_usage();
        }

        $durationNs = $seconds * 1e9;
        $start = hrtime(true);

        $runs = [];
        do {
            $runs[] = $this->measure($callback);
            $end = hrtime(true);
        } while (($end - $start) < $durationNs);

        gc_collect_cycles();

        return BenchmarkResult::fromRuns($runs, $end - $start, memory_get_peak_usage(true));
    }

    protected function measure(\Closure $callback): int
    {
        $start = hrtime(true);

        $callback();

        return hrtime(true) - $start;
    }
}
