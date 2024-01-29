<?php

namespace Keepsuit\Liquid\Performance\Shopify;

use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Filters\FiltersProvider;

class CustomFilters extends FiltersProvider
{
    public function json(mixed $value): string
    {
        if (is_array($value) && array_key_exists('collections', $value)) {
            $value = [...$value];
            unset($value['collections']);
        }

        return json_encode($value, flags: JSON_THROW_ON_ERROR);
    }

    public function moneyWithCurrency(int|float|null $money): string
    {
        if ($money === null) {
            return '';
        }

        return sprintf('$ %.2f USD', $money / 100);
    }

    public function money(int|float|null $money): string
    {
        if ($money === null) {
            return '';
        }

        return sprintf('$ %.2f', $money / 100);
    }

    public function currency(): string
    {
        return 'USD';
    }

    public function weight(int|float $grams): string
    {
        return sprintf('%.2f', $grams / 1000);
    }

    public function weightWithUnit(int|float $grams): string
    {
        return $this->weight($grams).' kg';
    }

    public function linkToTag(string|int|float $label, string $tag): string
    {
        return sprintf(
            '<a title="Show tag %s" href="/collections/%s/%s">%s</a>',
            $tag,
            $this->context->get('handle'),
            $tag,
            $label
        );
    }

    public function highlightActiveTag(string|int|float $tag, string $cssClass = 'active'): string
    {
        $currentTags = $this->context->get('current_tags') ?? [];
        assert(is_array($currentTags));

        if (in_array($tag, $currentTags)) {
            return sprintf('<span class="%s">%s</span>', $cssClass, $tag);
        }

        return (string) $tag;
    }

    public function linkToAddTag(string|int|float $label, string $tag): string
    {
        $currentTags = $this->context->get('current_tags') ?? [];
        assert(is_array($currentTags));
        $tags = array_unique([...$currentTags, $tag]);

        return sprintf(
            '<a title="Show tag %s" href="/collections/%s/%s">%s</a>',
            $tag,
            $this->context->get('handle'),
            implode('+', $tags),
            $label
        );
    }

    public function linkToRemoveTag(string|int|float $label, string $tag): string
    {
        $currentTags = $this->context->get('current_tags') ?? [];
        assert(is_array($currentTags));
        $tags = array_filter($currentTags, fn ($t) => $t !== $tag);

        return sprintf(
            '<a title="Show tag %s" href="/collections/%s/%s">%s</a>',
            $tag,
            $this->context->get('handle'),
            implode('+', $tags),
            $label
        );
    }

    public function assetUrl(string $input): string
    {
        return "/files/1/[shop_id]/[shop_id]/assets/$input";
    }

    public function globalAssetUrl(string $input): string
    {
        return "/global/$input";
    }

    public function shopifyAssetUrl(string $input): string
    {
        return "/shopify/$input";
    }

    public function scriptTag(string $url): string
    {
        return sprintf('<script src="%s" type="text/javascript"></script>', $url);
    }

    public function stylesheetTag(string $url, string $media = 'all'): string
    {
        return sprintf('<link href="%s" rel="stylesheet" type="text/css" media="%s" />', $url, $media);
    }

    public function linkTo(string $link, string $url, string $title = ''): string
    {
        return sprintf('<a href="%s" title="%s">%s</a>', $url, $title, $link);
    }

    public function imgTag(string $url, string $alt = ''): string
    {
        return sprintf('<img src="%s" alt="%s" />', $url, $alt);
    }

    public function linkToVendor(?string $vendor): string
    {
        if ($vendor !== null) {
            return $this->linkTo($this->urlForVendor($vendor), $vendor);
        }

        return 'Unknown vendor';
    }

    public function linkToType(?string $type): string
    {
        if ($type !== null) {
            return $this->linkTo($this->urlForType($type), $type);
        }

        return 'Unknown vendor';
    }

    public function urlForVendor(string $vendor): string
    {
        return '/collections/'.$this->toHandle($vendor);
    }

    public function urlForType(string $type): string
    {
        return '/collections/'.$this->toHandle($type);
    }

    public function productImgUrl(string $url, string $style = 'small'): string
    {
        if (preg_match('/\Aproducts\/([\w\-_]+)\.(\w{2,4})/', $url, $matches) === 0) {
            throw new InvalidArgumentException('filter "size" can only be called on product images');
        }

        $filename = $matches[1];
        $extension = $matches[2];

        return match ($style) {
            'original' => '/files/shops/random_number/'.$url,
            'grande', 'large', 'medium', 'compact', 'small', 'thumb', 'icon' => sprintf('/files/shops/random_number/products/%s_%s.%s', $filename, $style, $extension),
            default => throw new InvalidArgumentException('valid parameters for filter "size" are: original, grande, large, medium, compact, small, thumb and icon '),
        };
    }

    public function defaultPagination(array $paginate): string
    {
        $html = [];

        if (isset($paginate['previous'])) {
            $html[] = '<span class="prev">'.$this->linkTo($paginate['previous']['title'], $paginate['previous']['url']).'</span>';
        }

        foreach ($paginate['parts'] as $part) {
            $html[] = match (true) {
                $part['is_link'] => '<span class="page">'.$this->linkTo($part['title'], $part['url']).'</span>',
                (int) $part['title'] == (int) $paginate['current_page'] => '<span class="page current">'.$part['title'].'</span>',
                default => '<span class="deco">'.$part['title'].'</span>',
            };
        }

        if (isset($paginate['next'])) {
            $html[] = '<span class="next">'.$this->linkTo($paginate['next']['title'], $paginate['next']['url']).'</span>';
        }

        return implode(' ', $html);
    }

    public function pluralize(int|float $input, string $singular, string $plural): string
    {
        return $input === 1 ? $singular : $plural;
    }

    protected function toHandle(string $input): string
    {
        $result = $input;
        $result = strtolower($result);
        $result = str_replace(['\'', '"', '()', '[]'], '', $result);
        $result = preg_replace('/\W+/', '-', $result) ?? $result;

        return trim($result, '-');
    }
}
