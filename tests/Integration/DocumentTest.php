<?php

test('unexpected outer tag', function () {
    $source = '{% else %}';
    assertMatchSyntaxError("Unexpected outer 'else' tag", $source);
});

test('unknown tag', function () {
    $source = '{% foo %}';
    assertMatchSyntaxError("Unknown tag 'foo'", $source);
});
