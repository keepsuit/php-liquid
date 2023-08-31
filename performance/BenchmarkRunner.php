<?php

namespace Keepsuit\Liquid\Performance;

class BenchmarkRunner
{
    public function run(int $seconds, \Closure $callback): BenchmarkResult
    {
        $durationNs = $seconds * 1e9;
        $start = hrtime(true);

        $runs = [];
        do {
            gc_collect_cycles();

            $runs[] = $this->measure($callback);
            $end = hrtime(true);
        } while (($end - $start) < $durationNs);

        return BenchmarkResult::fromRuns($runs, $end - $start);
    }

    protected function measure(\Closure $callback): int
    {
        $start = hrtime(true);

        $callback();

        return hrtime(true) - $start;
    }
}
