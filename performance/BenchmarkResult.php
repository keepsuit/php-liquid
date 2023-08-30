<?php

namespace Keepsuit\Liquid\Performance;

class BenchmarkResult
{
    public readonly float $errorPercentage;

    public function __construct(
        public readonly int $runsCount,
        public readonly int $durationNs,
        public readonly int $averageNs,
        public readonly float $standardDeviation,
        public readonly float $ops,
    ) {
        $this->errorPercentage = round($this->standardDeviation / $this->averageNs * 100, 2);
    }

    public function durationMs(): float
    {
        return $this->durationNs / 1e6;
    }

    public function durationS(): float
    {
        return $this->durationNs / 1e9;
    }

    public function averageMs(): float
    {
        return $this->averageNs / 1e6;
    }

    public static function fromRuns(array $runs, int $durationNs): BenchmarkResult
    {
        $runsCount = count($runs);
        $averageNs = (int) (array_sum($runs) / $runsCount);
        $ops = $runsCount / ($durationNs / 1e9);
        $standardDeviation = static::computeStandardDeviation($runs, $averageNs);

        return new BenchmarkResult(
            runsCount: $runsCount,
            durationNs: $durationNs,
            averageNs: $averageNs,
            standardDeviation: $standardDeviation,
            ops: $ops,
        );
    }

    protected static function computeStandardDeviation(array $runs, float $averageNs): float
    {
        $var = 0.0;

        foreach ($runs as $run) {
            $var += pow(($run - $averageNs), 2);
        }

        return sqrt($var / count($runs));
    }
}
