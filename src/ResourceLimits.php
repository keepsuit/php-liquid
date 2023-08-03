<?php

namespace Keepsuit\Liquid;

class ResourceLimits
{
    protected int $renderScore = 0;

    protected int $assignScore = 0;

    public function __construct(
        protected ?int $renderLengthLimit = null,
        protected ?int $renderScoreLimit = null,
        protected ?int $assignScoreLimit = null,
    ) {
    }

    /**
     * @throws ResourceLimitException
     */
    public function incrementRenderScore(int $amount = 1): ResourceLimits
    {
        $this->renderScore += $amount;

        if ($this->renderScoreLimit != null && $this->renderScoreLimit < $this->renderScore) {
            throw new ResourceLimitException('Memory limit exceeded');
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
            throw new ResourceLimitException('Memory limit exceeded');
        }

        return $this;
    }

    public function reset(): ResourceLimits
    {
        $this->renderScore = 0;
        $this->assignScore = 0;

        return $this;
    }
}
