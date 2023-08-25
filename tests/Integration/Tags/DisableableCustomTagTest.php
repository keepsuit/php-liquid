<?php

use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\TagBlock;
use Keepsuit\Liquid\Template;

beforeEach(function () {
    Template::registerTag(CustomTag::class);
    Template::registerTag(Custom2Tag::class);
});

afterEach(function () {
    Template::deleteTag(CustomTag::class);
    Template::deleteTag(Custom2Tag::class);
    Template::deleteTag(DisableCustomTag::class);
    Template::deleteTag(DisableBothTag::class);
});

test('block tag disabling nested tag', function () {
    Template::registerTag(DisableCustomTag::class);

    expect(renderTemplate('{% disable %}{% custom %};{% custom2 %}{% enddisable %}', renderErrors: true))
        ->toBe('Liquid error (line 1): custom usage is not allowed in this context;custom2');
});

test('block tag disabling multiple tags', function () {
    Template::registerTag(DisableBothTag::class);

    expect(renderTemplate('{% disable %}{% custom %};{% custom2 %}{% enddisable %}', renderErrors: true))
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
