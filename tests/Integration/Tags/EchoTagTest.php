<?php

test('echo outputs its input', function () {
    assertTemplateResult('BAR', '{%- echo variable-name | upcase -%}', assigns: ['variable-name' => 'bar']);
});
