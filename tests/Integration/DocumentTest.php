<?php

test('unknown tag', function () {
    $source = '{% foo %}';
    assertMatchSyntaxError("Liquid syntax error (line 1): Unknown tag 'foo'", $source);
});
