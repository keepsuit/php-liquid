<?php

namespace Keepsuit\Liquid\Performance\Shopify;

use Keepsuit\Liquid\Support\Arr;
use Symfony\Component\Yaml\Yaml;

class Database
{
    protected const DATABASE_FILE_PATH = __DIR__.'/vision.database.yml';

    protected static ?array $tables = null;

    public static function tables(): array
    {
        if (static::$tables !== null) {
            return static::$tables;
        }

        $database = (array) Yaml::parseFile(static::DATABASE_FILE_PATH);
        $products = is_array($database['products'] ?? null) ? $database['products'] : [];
        $collections = is_array($database['collections'] ?? null) ? $database['collections'] : [];
        $blogs = is_array($database['blogs'] ?? null) ? $database['blogs'] : [];
        $lineItems = is_array($database['line_items'] ?? null) ? $database['line_items'] : [];

        $database['products'] = array_map(
            function (array $product) use ($collections) {
                $collections = array_filter(
                    $collections,
                    fn (array $collection) => Arr::first(
                        is_array($collection['products'] ?? null) ? $collection['products'] : [],
                        fn (array $p) => $p['id'] === $product['id']
                    ) !== null
                );
                $product['collections'] = array_values($collections);

                return $product;
            },
            $products
        );

        $tables = [];
        foreach ($database as $key => $values) {
            if (! is_array($values)) {
                continue;
            }

            $tables[$key] = array_reduce($values, function (array $acc, mixed $item) {
                if (! is_array($item)) {
                    return $acc;
                }

                if (isset($item['handle']) && is_string($item['handle'])) {
                    $acc[$item['handle']] = $item;
                } else {
                    $acc[] = $item;
                }

                return $acc;
            }, []);
        }

        $tables['collection'] = Arr::first($collections);
        $tables['product'] = Arr::first($database['products']);
        $tables['blog'] = Arr::first($blogs);
        $articles = $tables['blog']['articles'] ?? [];
        assert(is_array($articles));
        $tables['article'] = Arr::first($articles);

        $tables['cart'] = [
            'total_price' => array_reduce($lineItems, function (int $total, mixed $item): int {
                if (! is_array($item)) {
                    return $total;
                }

                $linePrice = $item['line_price'] ?? 0;
                $quantity = $item['quantity'] ?? 0;
                if (! is_int($linePrice) && ! is_float($linePrice)) {
                    $linePrice = 0;
                }
                if (! is_int($quantity) && ! is_float($quantity)) {
                    $quantity = 0;
                }

                return (int) ($total + $linePrice * $quantity);
            }, 0),
            'item_count' => array_reduce($lineItems, function (int $total, mixed $item): int {
                if (! is_array($item)) {
                    return $total;
                }

                $quantity = $item['quantity'] ?? 0;
                if (! is_int($quantity) && ! is_float($quantity)) {
                    $quantity = 0;
                }

                return (int) ($total + $quantity);
            }, 0),
            'items' => $lineItems,
        ];

        return static::$tables = $tables;
    }
}
