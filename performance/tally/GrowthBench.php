<?php

namespace Keepsuit\Liquid\Performance\tally;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Performance\Support\Database;
use Keepsuit\Liquid\Performance\Support\Drops\ProductDrop;
use Tally\Attribute\Before;
use Tally\Attribute\Constant;
use Tally\Attribute\DataProvider;
use Tally\Attribute\Linear;
use Tally\Attribute\Warmup;

/**
 * Tally-only: checks how cost grows with input size instead of pinning numbers.
 * Kept outside performance/benchmarks because PHPBench cannot run data providers.
 */
#[Warmup(1)]
final class GrowthBench
{
    private Environment $environment;

    /**
     * @return iterable<string, array{int}>
     */
    public static function rangeSizes(): iterable
    {
        foreach ([10, 1_000, 1_000_000] as $size) {
            yield 'size-'.$size => [$size];
        }
    }

    /**
     * @return iterable<string, array{list<ProductDrop>}>
     */
    public static function productLists(): iterable
    {
        $products = Database::products();

        foreach ([4, 24, 96] as $size) {
            yield 'size-'.$size => [array_slice(array_merge(...array_fill(0, 4, $products)), 0, $size)];
        }
    }

    /**
     * @return iterable<string, array{list<array{tags: list<int>}>}>
     */
    public static function recordLists(): iterable
    {
        foreach ([4, 24, 96] as $size) {
            $records = [];
            for ($i = 0; $i < $size; $i++) {
                $records[] = ['tags' => range($i % 4, $i % 4 + 255)];
            }

            yield 'size-'.$size => [$records];
        }
    }

    #[Before]
    public function setUp(): void
    {
        $this->environment = EnvironmentFactory::new()->build();
    }

    /** `limit` must stop the loop without materializing the range. */
    #[DataProvider('rangeSizes')]
    #[Constant('size')]
    public function benchLimitedRange(int $size): void
    {
        $this->environment->parseString('{% for i in (1..'.$size.') limit:5 %}{{ i }}{% endfor %}')
            ->render($this->environment->newRenderContext());
    }

    /**
     * @param  list<ProductDrop>  $products
     */
    #[DataProvider('productLists')]
    #[Linear('products')]
    public function benchProductLoop(array $products): void
    {
        $this->environment->parseString('{% for product in products %}{{ product.title }}{% endfor %}')
            ->render($this->environment->newRenderContext(staticData: ['products' => $products]));
    }

    /**
     * @param  list<array{tags: list<int>}>  $records
     */
    #[DataProvider('recordLists')]
    #[Linear('records')]
    public function benchUniqNestedLists(array $records): void
    {
        $this->environment->parseString("{{ records | uniq: 'tags' | size }}")
            ->render($this->environment->newRenderContext(data: ['records' => $records]));
    }
}
