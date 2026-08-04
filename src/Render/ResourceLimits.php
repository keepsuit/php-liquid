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

    protected int $streamedLength = 0;

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

        if ($this->renderScoreLimit !== null && $this->renderScoreLimit < $this->renderScore) {
            $this->throwLimitReachedException();
        }

        if ($this->cumulativeRenderScoreLimit !== null && $this->cumulativeRenderScoreLimit < $this->cumulativeRenderScore) {
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

        if ($this->assignScoreLimit !== null && $this->assignScoreLimit < $this->assignScore) {
            $this->throwLimitReachedException();
        }

        if ($this->cumulativeAssignScoreLimit !== null && $this->cumulativeAssignScoreLimit < $this->cumulativeAssignScore) {
            $this->throwLimitReachedException();
        }

        return $this;
    }

    /**
     * Called from exactly one place, Template::render() on the root template.
     * The root output already contains every node's output, including output
     * from nodes defined outside this library, so nested bodies have nothing
     * to add and custom nodes have nothing to remember.
     *
     * @throws ResourceLimitException
     */
    public function incrementWriteScore(string $output): ResourceLimits
    {
        if ($this->renderLengthLimit !== null && strlen($output) > $this->renderLengthLimit) {
            $this->throwLimitReachedException();
        }

        return $this;
    }

    /**
     * Streaming counterpart of incrementWriteScore: chunks arrive one at a time,
     * so the length limit has to be checked against a running total instead of
     * the length of a single chunk.
     *
     * Called from exactly one place, Template::stream() on the root template,
     * for the same reason incrementWriteScore is.
     *
     * The total is reset per root stream by resetStreamWriteScore(), so the
     * limit caps one stream rather than the lifetime of the context — the same
     * scope the render path applies to a single render() call.
     *
     * @throws ResourceLimitException
     */
    public function incrementStreamWriteScore(string $output): void
    {
        if ($this->renderLengthLimit === null) {
            return;
        }

        $this->streamedLength += strlen($output);

        if ($this->streamedLength > $this->renderLengthLimit) {
            $this->throwLimitReachedException();
        }
    }

    public function resetStreamWriteScore(): void
    {
        $this->streamedLength = 0;
    }

    public function reset(): ResourceLimits
    {
        $this->renderScore = 0;
        $this->streamedLength = 0;
        $this->assignScore = 0;
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

    /**
     * @param  Closure(): string  $closure
     * @return string
     *
     * @throws ResourceLimitException
     */
    public function withCapture(Closure $closure): mixed
    {
        $result = $closure();

        if (is_string($result)) {
            $this->incrementAssignScore(strlen($result));
        }

        return $result;
    }
}
