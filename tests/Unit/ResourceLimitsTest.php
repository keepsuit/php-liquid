<?php

use Keepsuit\Liquid\Exceptions\ResourceLimitException;
use Keepsuit\Liquid\Render\ResourceLimits;

test('resource limits default cumulative state', function () {
    $limits = new ResourceLimits;

    expect($limits->cumulativeRenderScoreLimit)->toBeNull()
        ->and($limits->cumulativeAssignScoreLimit)->toBeNull()
        ->and($limits->getCumulativeRenderScore())->toBe(0)
        ->and($limits->getCumulativeAssignScore())->toBe(0);
});

test('resource limits constructor configures cumulative limits', function () {
    $limits = new ResourceLimits(
        cumulativeRenderScoreLimit: 10,
        cumulativeAssignScoreLimit: 20,
    );

    expect($limits->cumulativeRenderScoreLimit)->toBe(10)
        ->and($limits->cumulativeAssignScoreLimit)->toBe(20);
});

test('resource limits clone preserves cumulative limits', function () {
    $limits = ResourceLimits::clone(new ResourceLimits(
        renderLengthLimit: 1,
        renderScoreLimit: 2,
        assignScoreLimit: 3,
        cumulativeRenderScoreLimit: 4,
        cumulativeAssignScoreLimit: 5,
    ));

    expect($limits->renderLengthLimit)->toBe(1)
        ->and($limits->renderScoreLimit)->toBe(2)
        ->and($limits->assignScoreLimit)->toBe(3)
        ->and($limits->cumulativeRenderScoreLimit)->toBe(4)
        ->and($limits->cumulativeAssignScoreLimit)->toBe(5);
});

test('resource limits reset leaves cumulative scores intact', function () {
    $limits = new ResourceLimits;
    $limits->incrementRenderScore(2);
    $limits->incrementAssignScore(3);

    $limits->reset();

    expect($limits->getRenderScore())->toBe(0)
        ->and($limits->getAssignScore())->toBe(0)
        ->and($limits->getCumulativeRenderScore())->toBe(2)
        ->and($limits->getCumulativeAssignScore())->toBe(3);
});

test('a zero limit rejects everything instead of disabling the limit', function () {
    expect(fn () => (new ResourceLimits(renderScoreLimit: 0))->incrementRenderScore(1))
        ->toThrow(ResourceLimitException::class);
    expect(fn () => (new ResourceLimits(assignScoreLimit: 0))->incrementAssignScore(1))
        ->toThrow(ResourceLimitException::class);
    expect(fn () => (new ResourceLimits(cumulativeRenderScoreLimit: 0))->incrementRenderScore(1))
        ->toThrow(ResourceLimitException::class);
    expect(fn () => (new ResourceLimits(cumulativeAssignScoreLimit: 0))->incrementAssignScore(1))
        ->toThrow(ResourceLimitException::class);
});

test('withCapture charges the returned string to the assign score', function () {
    $limits = new ResourceLimits;

    expect($limits->withCapture(fn () => 'abcd'))->toBe('abcd')
        ->and($limits->getAssignScore())->toBe(4);

    // Nested, as {% capture %} inside {% capture %}: two variables, both charged.
    $limits = new ResourceLimits;
    $limits->withCapture(fn () => $limits->withCapture(fn () => 'abcd'));
    expect($limits->getAssignScore())->toBe(8);

    // The length is read off the returned string, so a closure that captures
    // without returning the text is charged nothing.
    $limits = new ResourceLimits;
    $limits->withCapture(fn () => null);
    expect($limits->getAssignScore())->toBe(0);
});

test('withCapture does not charge the render length limit', function () {
    // Captured text is assigned, not written, so only the assign limit applies.
    $limits = new ResourceLimits(renderLengthLimit: 2);
    expect($limits->withCapture(fn () => 'abcd'))->toBe('abcd');

    $limits = new ResourceLimits(assignScoreLimit: 2);
    expect(fn () => $limits->withCapture(fn () => 'abcd'))
        ->toThrow(ResourceLimitException::class);
});

test('resource limits cumulative render score limit', function () {
    $limits = new ResourceLimits(cumulativeRenderScoreLimit: 3);

    $limits->incrementRenderScore(2);

    expect(fn () => $limits->incrementRenderScore(2))->toThrow(ResourceLimitException::class);
    expect($limits->reached())->toBeTrue();
});

test('resource limits cumulative assign score limit', function () {
    $limits = new ResourceLimits(cumulativeAssignScoreLimit: 3);

    $limits->incrementAssignScore(2);

    expect(fn () => $limits->incrementAssignScore(2))->toThrow(ResourceLimitException::class);
    expect($limits->reached())->toBeTrue();
});
