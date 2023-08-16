<?php

test('break with no block', function () {
    assertTemplateResult('before', 'before{% break %}after');
});
