<?php

describe('rendering with template backends', function () {
    test('echo outputs its input', function (bool $compiled) {
        assertTemplateResult('BAR', '{%- echo variable-name | upcase -%}', staticData: ['variable-name' => 'bar'], compiled: $compiled);
    });
})->with('template backends');
