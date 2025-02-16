<?php

test('echo outputs its input', function () {
    assertTemplateResult('BAR', '{%- echo variable-name | upcase -%}', staticData: ['variable-name' => 'bar']);
});
