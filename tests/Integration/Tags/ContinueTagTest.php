<?php

test('continue with no block', function () {
    assertTemplateResult('', '{% continue %}');
});
