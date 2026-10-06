<?php

describe('rendering with template backends', function () {
    test('break with no block', function (bool $compiled) {
        assertTemplateResult('before', 'before{% break %}after', compiled: $compiled);
    });
})->with('template backends');
