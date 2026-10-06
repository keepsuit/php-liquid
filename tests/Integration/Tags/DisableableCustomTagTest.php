<?php

use Keepsuit\Liquid\Compiler\CompilerContext;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Parse\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\TagBlock;

beforeEach(function () {
    $this->templateFactory = EnvironmentFactory::new()
        ->registerTag(CustomTag::class)
        ->registerTag(Custom2Tag::class);
});

describe('rendering with template backends', function () {
    test('block tag disabling nested tag', function (bool $compiled) {
        $this->templateFactory->registerTag(DisableCustomTag::class);

        expect(renderTemplate('{% disable %}{% custom %};{% custom2 %}{% enddisable %}', renderErrors: true, factory: $this->templateFactory, compiled: $compiled))
            ->toBe('Liquid error (line 1): custom usage is not allowed in this context;custom2');
    });

    test('block tag disabling multiple tags', function (bool $compiled) {
        $this->templateFactory->registerTag(DisableBothTag::class);

        expect(renderTemplate('{% disable %}{% custom %};{% custom2 %}{% enddisable %}', renderErrors: true, factory: $this->templateFactory, compiled: $compiled))
            ->toBe('Liquid error (line 1): custom usage is not allowed in this context;Liquid error (line 1): custom2 usage is not allowed in this context');
    });

    test('compiled block tag disabling nested compiled tag', function (bool $compiled) {
        $factory = EnvironmentFactory::new()
            ->registerTag(CompiledCustomTag::class)
            ->registerTag(CompiledDisableCustomTag::class);
        $source = '{% disable %}{% custom %}{% enddisable %};{% custom %}';
        $expected = 'Liquid error (line 1): custom usage is not allowed in this context;custom';

        expect(renderTemplate($source, renderErrors: true, factory: $factory, compiled: $compiled))->toBe($expected);
        expect(implode('', iterator_to_array(streamTemplate($source, renderErrors: true, factory: $factory, compiled: $compiled), false)))->toBe($expected);
    });
})->with('template backends');

class CustomTag extends Tag implements Disableable
{
    public static function tagName(): string
    {
        return 'custom';
    }

    public function render(RenderContext $context): string
    {
        return static::tagName();
    }

    public function parse(TagParseContext $context): static
    {
        return $this;
    }
}

class Custom2Tag extends Tag implements Disableable
{
    public static function tagName(): string
    {
        return 'custom2';
    }

    public function render(RenderContext $context): string
    {
        return static::tagName();
    }

    public function parse(TagParseContext $context): static
    {
        return $this;
    }
}

class DisableCustomTag extends TagBlock
{
    protected ?BodyNode $body;

    public static function tagName(): string
    {
        return 'disable';
    }

    public function parse(TagParseContext $context): static
    {
        $this->body = $context->body;

        return $this;
    }

    public function render(RenderContext $context): string
    {
        return $context->withDisabledTags(['custom'], fn () => $this->body?->render($context) ?? '');
    }
}

class DisableBothTag extends TagBlock
{
    protected ?BodyNode $body;

    public static function tagName(): string
    {
        return 'disable';
    }

    public function parse(TagParseContext $context): static
    {
        $this->body = $context->body;

        return $this;
    }

    public function render(RenderContext $context): string
    {
        return $context->withDisabledTags(['custom', 'custom2'], fn () => $this->body?->render($context) ?? '');
    }
}

class CompiledCustomTag extends CustomTag implements CanBeCompiled
{
    public function compile(CompilerContext $context): void
    {
        $context->writeText(static::tagName());
    }
}

class CompiledDisableCustomTag extends DisableCustomTag implements CanBeCompiled
{
    public function compile(CompilerContext $context): void
    {
        assert($this->body !== null);

        $output = $context->temporaryVariable();
        $context->write($output.' = $context->withDisabledTags([\'custom\'], function () use ($context): string {');
        $context->indent();
        $context->writeRenderedBody($this->body, '$body');
        $context->write('return $body;');
        $context->outdent()->write('});');
        $context->writeOutput($output);
    }
}
