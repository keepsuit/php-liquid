<?php

use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\TagBlock;

beforeEach(function () {
    $this->templateFactory = \Keepsuit\Liquid\TemplateFactory::new()
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

    public function render(Context $context): string
    {
        return static::tagName();
    }
}

class Custom2Tag extends Tag implements Disableable
{
    public static function tagName(): string
    {
        return 'custom2';
    }

    public function render(Context $context): string
    {
        return static::tagName();
    }
}

class DisableCustomTag extends TagBlock
{
    public static function tagName(): string
    {
        return 'disable';
    }

    public function disabledTags(): array
    {
        return ['custom'];
    }
}

class DisableBothTag extends TagBlock
{
    public static function tagName(): string
    {
        return 'disable';
    }

    public function disabledTags(): array
    {
        return ['custom', 'custom2'];
    }
}
