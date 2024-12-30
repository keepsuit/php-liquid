<?php

beforeEach(function () {
    $this->factory = \Keepsuit\Liquid\EnvironmentFactory::new()
        ->setStrictVariables(true)
        ->registerFilters(\Keepsuit\Liquid\Filters\Custom\TernaryFilter::class);
});

test('ternary', function () {
    expect(renderTemplate('{{ true | ternary: "yes", "no" }}', factory: $this->factory))->toBe('yes');
    expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, assigns: ['test' => ['a']]))->toBe('yes');
    expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, assigns: ['test' => 'a']))->toBe('yes');
    expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, assigns: ['test' => new \Keepsuit\Liquid\Tests\Stubs\IntegerDrop('1')]))->toBe('yes');

    expect(renderTemplate('{{ false | ternary: "yes", "no" }}', factory: $this->factory))->toBe('no');
    expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, assigns: ['test' => []]))->toBe('no');
    expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, assigns: ['test' => '']))->toBe('no');
    expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, assigns: ['test' => null]))->toBe('no');
});
