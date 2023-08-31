<?php

namespace Keepsuit\Liquid\Performance\Shopify;

use Keepsuit\Liquid\Support\Arr;
use Keepsuit\Liquid\Support\YamlParser;

class Database
{
    protected const DATABASE_FILE_PATH = __DIR__.'/vision.database.yml';

    protected static ?array $tables = null;

    public static function tables(): array
    {
        if (static::$tables !== null) {
            return static::$tables;
        }

        $database = YamlParser::parseFile(static::DATABASE_FILE_PATH);

        foreach ($database['products'] as $product) {
            $collections = array_filter(
                $database['collections'],
                fn (array $collection) => Arr::first($collection['products'], fn (array $p) => $p['id'] === $product['id']) !== null
            );
            $product['collections'] = array_values($collections);
        }

        $tables = [];
        foreach ($database as $key => $values) {
            $tables[$key] = array_reduce($values, function (array $acc, array $item) {
                if (isset($item['handle'])) {
                    $acc[$item['handle']] = $item;
                } else {
                    $acc[] = $item;
                }

                return $acc;
            }, []);
        }

        $tables['collection'] = Arr::first($database['collections']);
        $tables['product'] = Arr::first($database['products']);
        $tables['blog'] = Arr::first($database['blogs']);
        $tables['article'] = Arr::first($tables['blog']['articles'] ?? []);

        $tables['cart'] = [
            'total_price' => array_reduce($tables['line_items'], fn (int $total, array $item) => $total + $item['line_price'] * $item['quantity'], 0),
            'item_count' => array_reduce($tables['line_items'], fn (int $total, array $item) => $total + $item['quantity'], 0),
            'items' => $database['line_items'],
        ];

        return static::$tables = $tables;
    }
}
