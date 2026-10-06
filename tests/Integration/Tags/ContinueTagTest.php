<?php

describe('rendering with template backends', function () {
    test('continue with no block', function (bool $compiled) {
        assertTemplateResult('', '{% continue %}', compiled: $compiled);
    });
})->with('template backends');
