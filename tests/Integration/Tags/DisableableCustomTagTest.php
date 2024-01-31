<?php

use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\TagBlock;
use Keepsuit\Liquid\TemplateFactory;

beforeEach(function () {
    $this->templateFactory = TemplateFactory::new()
        ->registerTag(CustomTag::class)
        ->registerTag(Custom2Tag::class);
});

test('block tag disabling nested tag', function () {
    $this->templateFactory->registerTag(DisableCustomTag::class);

    expect(renderTemplate('{% disable %}{% custom %};{% custom2 %}{% enddisable %}', renderErrors: true, factory: $this->templateFactory))
        ->toBe('Liquid error (line 1): custom usage is not allowed in this context;custom2');
});

test('block tag disabling multiple tags', function () {
    $this->templateFactory->registerTag(DisableBothTag::class);

    expect(renderTemplate('{% disable %}{% custom %};{% custom2 %}{% enddisable %}', renderErrors: true, factory: $this->templateFactory))
        ->toBe('Liquid error (line 1): custom usage is not allowed in this context;Liquid error (line 1): custom2 usage is not allowed in this context');
});

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
