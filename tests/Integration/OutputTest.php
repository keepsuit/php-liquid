<?php

test('variable', function () {
    assertTemplateResult(' bmw ', ' {{best_cars}} ', ['best_cars' => 'bmw']);
});
