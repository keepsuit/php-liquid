<?php

namespace Keepsuit\Liquid\Render;

use Closure;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;

class ResourceLimits
{
    protected int $renderScore = 0;

    protected int $cumulativeRenderScore = 0;

    protected int $assignScore = 0;

    protected int $cumulativeAssignScore = 0;

    protected ?int $lastCaptureLength = null;

    protected bool $reachedLimit = false;

    public function __construct(
        public readonly ?int $renderLengthLimit = null,
        public readonly ?int $renderScoreLimit = null,
        public readonly ?int $assignScoreLimit = null,
        public readonly ?int $cumulativeRenderScoreLimit = null,
        public readonly ?int $cumulativeAssignScoreLimit = null,
    ) {}

    public static function clone(ResourceLimits $limits): ResourceLimits
    {
        return new ResourceLimits(
            renderLengthLimit: $limits->renderLengthLimit,
            renderScoreLimit: $limits->renderScoreLimit,
            assignScoreLimit: $limits->assignScoreLimit,
            cumulativeRenderScoreLimit: $limits->cumulativeRenderScoreLimit,
            cumulativeAssignScoreLimit: $limits->cumulativeAssignScoreLimit,
        );
    }

    /**
     * @throws ResourceLimitException
     */
    public function incrementRenderScore(int $amount = 1): ResourceLimits
    {
        $this->renderScore += $amount;
        $this->cumulativeRenderScore += $amount;

        if ($this->renderScoreLimit != null && $this->renderScoreLimit < $this->renderScore) {
            $this->throwLimitReachedException();
        }

        if ($this->cumulativeRenderScoreLimit != null && $this->cumulativeRenderScoreLimit < $this->cumulativeRenderScore) {
            $this->throwLimitReachedException();
        }

        return $this;
    }

    /**
     * @throws ResourceLimitException
     */
    public function incrementAssignScore(int $amount = 1): ResourceLimits
    {
        $this->assignScore += $amount;
        $this->cumulativeAssignScore += $amount;

        if ($this->assignScoreLimit != null && $this->assignScoreLimit < $this->assignScore) {
            $this->throwLimitReachedException();
        }

        if ($this->cumulativeAssignScoreLimit != null && $this->cumulativeAssignScoreLimit < $this->cumulativeAssignScore) {
            $this->throwLimitReachedException();
        }

        return $this;
    }

    /**
     * @throws ResourceLimitException
     */
    public function incrementWriteScore(string $output): ResourceLimits
    {
        if (($lastCaptured = $this->lastCaptureLength) !== null) {
            $captured = strlen($output);
            $increment = $captured - $lastCaptured;
            $this->lastCaptureLength = $captured;
            $this->incrementAssignScore($increment);

            return $this;
        }

        if ($this->renderLengthLimit !== null && strlen($output) > $this->renderLengthLimit) {
            $this->throwLimitReachedException();
        }

        return $this;
    }

    public function reset(): ResourceLimits
    {
        $this->renderScore = 0;
        $this->assignScore = 0;
        $this->lastCaptureLength = null;
        $this->reachedLimit = false;

        return $this;
    }

    public function throwLimitReachedException(): void
    {
        $this->reachedLimit = true;

        throw new ResourceLimitException;
    }

    public function reached(): bool
    {
        return $this->reachedLimit;
    }

    public function getAssignScore(): int
    {
        return $this->assignScore;
    }

    public function getCumulativeAssignScore(): int
    {
        return $this->cumulativeAssignScore;
    }

    public function getRenderScore(): int
    {
        return $this->renderScore;
    }

    public function getCumulativeRenderScore(): int
    {
        return $this->cumulativeRenderScore;
    }

    public function withCapture(Closure $closure): mixed
    {
        $oldCaptureLength = $this->lastCaptureLength;

        $this->lastCaptureLength = 0;

        $result = $closure();

        $this->lastCaptureLength = $oldCaptureLength;

        return $result;
    }
}
