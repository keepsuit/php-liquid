<?php

test('increment', function () {
    assertTemplateResult('0 0', '{%increment port %} {{ port }}', staticData: ['port' => 10]);
    assertTemplateResult(' 0 1 1', '{{port}} {%increment port %} {%increment port%} {{port}}');
    assertTemplateResult(
        '0|0|1|2|1',
        <<<'LIQUID'
        {%- increment port %}|
        {%- increment starboard %}|
        {%- increment port %}|
        {%- increment port %}|
        {%- increment starboard %}
        LIQUID
    );
});

test('decrement', function () {
    assertTemplateResult('-1 -1', '{%decrement port %} {{ port }}', staticData: ['port' => 10]);
    assertTemplateResult(' -1 -2 -2', '{{port}} {%decrement port %} {%decrement port%} {{port}}');
    assertTemplateResult(
        '0|1|2|0|3|1|0|2',
        <<<'LIQUID'
        {%- increment starboard %}|
        {%- increment starboard %}|
        {%- increment starboard %}|
        {%- increment port %}|
        {%- increment starboard %}|
        {%- increment port %}|
        {%- decrement port %}|
        {%- decrement starboard %}
        LIQUID
    );
});
