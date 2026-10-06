<?php

beforeEach(function () {
    $this->factory = \Keepsuit\Liquid\EnvironmentFactory::new()
        ->setStrictVariables(true)
        ->registerFilters(\Keepsuit\Liquid\Filters\Custom\TernaryFilter::class);
});

describe('rendering with template backends', function () {
    test('ternary', function (bool $compiled) {
        expect(renderTemplate('{{ true | ternary: "yes", "no" }}', factory: $this->factory, compiled: $compiled))->toBe('yes');
        expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, staticData: ['test' => ['a']], compiled: $compiled))->toBe('yes');
        expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, staticData: ['test' => 'a'], compiled: $compiled))->toBe('yes');
        expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, staticData: ['test' => new \Keepsuit\Liquid\Tests\Stubs\IntegerDrop('1')], compiled: $compiled))->toBe('yes');

        expect(renderTemplate('{{ false | ternary: "yes", "no" }}', factory: $this->factory, compiled: $compiled))->toBe('no');
        expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, staticData: ['test' => []], compiled: $compiled))->toBe('no');
        expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, staticData: ['test' => ''], compiled: $compiled))->toBe('no');
        expect(renderTemplate('{{ test | ternary: "yes", "no" }}', factory: $this->factory, staticData: ['test' => null], compiled: $compiled))->toBe('no');
    });
})->with('template backends');
