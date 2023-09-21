<?php

namespace Keepsuit\Liquid\Render;

use Closure;
use Keepsuit\Liquid\Exceptions\ResourceLimitException;

class ResourceLimits
{
    protected int $renderScore = 0;

    protected int $assignScore = 0;

    protected int $renderLength = 0;

    protected ?int $lastCaptureLength = null;

    protected bool $reachedLimit = false;

    public function __construct(
        public readonly ?int $renderLengthLimit = null,
        public readonly ?int $renderScoreLimit = null,
        public readonly ?int $assignScoreLimit = null,
    ) {
    }

    /**
     * @throws ResourceLimitException
     */
    public function incrementRenderScore(int $amount = 1): ResourceLimits
    {
        $this->renderScore += $amount;

        if ($this->renderScoreLimit != null && $this->renderScoreLimit < $this->renderScore) {
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

        if ($this->assignScoreLimit != null && $this->assignScoreLimit < $this->assignScore) {
            $this->throwLimitReachedException();
        }

        return $this;
    }

    /**
     * @throws ResourceLimitException
     */
    public function incrementWriteScore(string $output): ResourceLimits
    {
        $this->renderLength += strlen($output);
        if ($this->renderLengthLimit !== null && $this->renderLength > $this->renderLengthLimit) {
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

        throw new ResourceLimitException();
    }

    public function reached(): bool
    {
        return $this->reachedLimit;
    }

    public function getAssignScore(): int
    {
        return $this->assignScore;
    }

    public function getRenderScore(): int
    {
        return $this->renderScore;
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
