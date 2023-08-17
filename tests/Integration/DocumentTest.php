<?php

test('unexpected outer tag', function () {
    $source = '{% else %}';
    assertMatchSyntaxError("Liquid syntax error (line 1): Unexpected outer 'else' tag", $source);
});

test('unknown tag', function () {
    $source = '{% foo %}';
    assertMatchSyntaxError("Liquid syntax error (line 1): Unknown tag 'foo'", $source);
});
