<?php

namespace Keepsuit\Liquid\Performance\Support;

use Keepsuit\Liquid\Performance\Support\Drops\ArticleDrop;
use Keepsuit\Liquid\Performance\Support\Drops\CartDrop;
use Keepsuit\Liquid\Performance\Support\Drops\CollectionDrop;
use Keepsuit\Liquid\Performance\Support\Drops\ImageDrop;
use Keepsuit\Liquid\Performance\Support\Drops\LinkDrop;
use Keepsuit\Liquid\Performance\Support\Drops\MetafieldsDrop;
use Keepsuit\Liquid\Performance\Support\Drops\PageDrop;
use Keepsuit\Liquid\Performance\Support\Drops\ProductDrop;
use Keepsuit\Liquid\Performance\Support\Drops\ShopDrop;
use Keepsuit\Liquid\Performance\Support\Drops\VariantDrop;

/**
 * Fixture data for the storefront benchmark theme.
 *
 * Two rules govern this class, and both exist to keep the benchmark measuring
 * the library rather than the fixture. See performance/README.md.
 *
 * 1. It assigns, it does not compute. No scans, no reductions, no sorting. Every
 *    derived value is either stored literally here or exposed as a drop method,
 *    where it becomes a measured part of template rendering.
 * 2. Nothing is memoized. Each call builds fresh drops, so #[Cache] starts cold
 *    on every render and no state is shared between benchmark revolutions.
 */
final class Database
{
    public const PRODUCTS_PER_PAGE = 24;

    /**
     * The rows live behind a method rather than a constant on purpose: PHPStan
     * treats a literal array this large as an `oversized-array` and collapses the
     * per-key types into one union, whereas it trusts a declared return type.
     *
     * @return non-empty-list<array{
     *     handle: string,
     *     title: string,
     *     vendor: string,
     *     type: string,
     *     description: string,
     *     price: int,
     *     compare_at: int,
     *     badges: list<string>,
     *     variants: non-empty-list<array{string, string, string, int, bool}>,
     *     images: non-empty-list<array{string, string, int, int}>,
     *     metafields: array<string, string>
     * }>
     */
    private static function productRows(): array
    {
        return [
            [
                'handle' => 'linen-shirt', 'title' => 'Linen Shirt', 'vendor' => 'ACME Apparel', 'type' => 'Shirts',
                'description' => 'A relaxed shirt in washed European linen, cut for long warm afternoons.',
                'price' => 3950, 'compare_at' => 5900, 'badges' => ['new', 'linen'],
                'variants' => [['linen-shirt-s', 'Small', 'S', 3950, true], ['linen-shirt-m', 'Medium', 'M', 3950, true], ['linen-shirt-l', 'Large', 'L', 4150, false]],
                'images' => [['/cdn/storefront/linen-shirt-front.jpg', 'Linen Shirt, front', 1200, 1500], ['/cdn/storefront/linen-shirt-detail.jpg', 'Linen Shirt, cuff detail', 1200, 1500]],
                'metafields' => ['care' => 'Machine wash cold, line dry', 'material' => '100% European linen'],
            ],
            [
                'handle' => 'canvas-tote', 'title' => 'Canvas Tote', 'vendor' => 'Field Goods', 'type' => 'Bags',
                'description' => 'A wide-mouthed tote in heavy cotton canvas that softens with every trip.',
                'price' => 2400, 'compare_at' => 3200, 'badges' => ['travel', 'sale'],
                'variants' => [['canvas-tote-natural', 'Natural', 'Natural', 2400, true], ['canvas-tote-slate', 'Slate', 'Slate', 2400, true], ['canvas-tote-olive', 'Olive', 'Olive', 2600, true]],
                'images' => [['/cdn/storefront/canvas-tote-front.jpg', 'Canvas Tote, front', 1200, 1200], ['/cdn/storefront/canvas-tote-inside.jpg', 'Canvas Tote, interior', 1200, 1200]],
                'metafields' => ['care' => 'Spot clean', 'material' => '18oz cotton canvas'],
            ],
            [
                'handle' => 'sun-hat', 'title' => 'Sun Hat', 'vendor' => 'Coastline', 'type' => 'Hats',
                'description' => 'A packable wide-brim hat woven from paper straw, with a grosgrain band.',
                'price' => 1875, 'compare_at' => 2500, 'badges' => ['summer', 'sale'],
                'variants' => [['sun-hat-s', 'Small', 'S', 1875, true], ['sun-hat-m', 'Medium', 'M', 1875, false], ['sun-hat-l', 'Large', 'L', 1875, true]],
                'images' => [['/cdn/storefront/sun-hat-front.jpg', 'Sun Hat, front', 1000, 1000], ['/cdn/storefront/sun-hat-brim.jpg', 'Sun Hat, brim', 1000, 1000]],
                'metafields' => ['care' => 'Reshape when damp', 'material' => 'Paper straw, cotton band'],
            ],
            [
                'handle' => 'ceramic-mug', 'title' => 'Ceramic Mug', 'vendor' => 'Studio Form', 'type' => 'Kitchen',
                'description' => 'A thrown stoneware mug with a matte glaze and a handle that fits four fingers.',
                'price' => 2200, 'compare_at' => 2200, 'badges' => ['kitchen', 'new'],
                'variants' => [['ceramic-mug-bone', 'Bone', 'Bone', 2200, true], ['ceramic-mug-ash', 'Ash', 'Ash', 2200, true], ['ceramic-mug-clay', 'Clay', 'Clay', 2400, false]],
                'images' => [['/cdn/storefront/ceramic-mug-front.jpg', 'Ceramic Mug, front', 1100, 1100], ['/cdn/storefront/ceramic-mug-stack.jpg', 'Ceramic Mug, stacked', 1100, 1100]],
                'metafields' => ['care' => 'Dishwasher safe', 'material' => 'Glazed stoneware'],
            ],
            [
                'handle' => 'weekend-bag', 'title' => 'Weekend Bag', 'vendor' => 'Field Goods', 'type' => 'Bags',
                'description' => 'Two nights of luggage with a leather base, brass hardware and a shoulder strap.',
                'price' => 8900, 'compare_at' => 10900, 'badges' => ['travel', 'limited'],
                'variants' => [['weekend-bag-tan', 'Tan', 'Tan', 8900, true], ['weekend-bag-black', 'Black', 'Black', 8900, true], ['weekend-bag-navy', 'Navy', 'Navy', 9400, true]],
                'images' => [['/cdn/storefront/weekend-bag-front.jpg', 'Weekend Bag, front', 1400, 1000], ['/cdn/storefront/weekend-bag-strap.jpg', 'Weekend Bag, strap', 1400, 1000]],
                'metafields' => ['care' => 'Condition leather twice a year', 'material' => 'Waxed canvas, bridle leather'],
            ],
            [
                'handle' => 'cotton-throw', 'title' => 'Cotton Throw', 'vendor' => 'North Loom', 'type' => 'Home',
                'description' => 'A loose-woven throw with hand-knotted fringe, warm enough for a cool evening.',
                'price' => 7600, 'compare_at' => 7600, 'badges' => ['home', 'soft'],
                'variants' => [['cotton-throw-oat', 'Oat', 'Oat', 7600, true], ['cotton-throw-indigo', 'Indigo', 'Indigo', 7600, false], ['cotton-throw-rust', 'Rust', 'Rust', 7900, true]],
                'images' => [['/cdn/storefront/cotton-throw-folded.jpg', 'Cotton Throw, folded', 1300, 1000], ['/cdn/storefront/cotton-throw-weave.jpg', 'Cotton Throw, weave', 1300, 1000]],
                'metafields' => ['care' => 'Wash warm, tumble low', 'material' => 'Organic cotton'],
            ],
            [
                'handle' => 'leather-wallet', 'title' => 'Leather Wallet', 'vendor' => 'Atelier No. 8', 'type' => 'Accessories',
                'description' => 'Four card slots and a note pocket, cut from a single piece of vegetable-tanned hide.',
                'price' => 5200, 'compare_at' => 6500, 'badges' => ['gift', 'sale'],
                'variants' => [['leather-wallet-chestnut', 'Chestnut', 'Chestnut', 5200, true], ['leather-wallet-black', 'Black', 'Black', 5200, true], ['leather-wallet-cognac', 'Cognac', 'Cognac', 5500, true]],
                'images' => [['/cdn/storefront/leather-wallet-front.jpg', 'Leather Wallet, front', 1000, 800], ['/cdn/storefront/leather-wallet-open.jpg', 'Leather Wallet, open', 1000, 800]],
                'metafields' => ['care' => 'Keep dry', 'material' => 'Vegetable-tanned leather'],
            ],
            [
                'handle' => 'travel-bottle', 'title' => 'Travel Bottle', 'vendor' => 'Coastline', 'type' => 'Kitchen',
                'description' => 'A vacuum bottle that keeps coffee hot to the end of a long train ride.',
                'price' => 2800, 'compare_at' => 2800, 'badges' => ['travel', 'summer'],
                'variants' => [['travel-bottle-steel', 'Steel', 'Steel', 2800, true], ['travel-bottle-sand', 'Sand', 'Sand', 2800, true], ['travel-bottle-forest', 'Forest', 'Forest', 3000, false]],
                'images' => [['/cdn/storefront/travel-bottle-front.jpg', 'Travel Bottle, front', 900, 1200], ['/cdn/storefront/travel-bottle-cap.jpg', 'Travel Bottle, cap', 900, 1200]],
                'metafields' => ['care' => 'Hand wash the lid', 'material' => '18/8 stainless steel'],
            ],
            [
                'handle' => 'desk-lamp', 'title' => 'Desk Lamp', 'vendor' => 'Studio Form', 'type' => 'Lighting',
                'description' => 'A counterweighted task lamp with a warm dimmable bulb and a fabric cord.',
                'price' => 6800, 'compare_at' => 8100, 'badges' => ['home', 'new'],
                'variants' => [['desk-lamp-brass', 'Brass', 'Brass', 6800, true], ['desk-lamp-black', 'Black', 'Black', 6800, true], ['desk-lamp-white', 'White', 'White', 6800, false]],
                'images' => [['/cdn/storefront/desk-lamp-lit.jpg', 'Desk Lamp, lit', 1200, 1400], ['/cdn/storefront/desk-lamp-arm.jpg', 'Desk Lamp, arm', 1200, 1400]],
                'metafields' => ['care' => 'Wipe with a dry cloth', 'material' => 'Steel, brass, linen cord'],
            ],
            [
                'handle' => 'wool-socks', 'title' => 'Wool Socks', 'vendor' => 'North Loom', 'type' => 'Socks',
                'description' => 'Ribbed merino socks with a reinforced heel, warm without the itch.',
                'price' => 1600, 'compare_at' => 1600, 'badges' => ['warm', 'gift'],
                'variants' => [['wool-socks-s', 'Small', 'S', 1600, true], ['wool-socks-m', 'Medium', 'M', 1600, true], ['wool-socks-l', 'Large', 'L', 1600, true]],
                'images' => [['/cdn/storefront/wool-socks-pair.jpg', 'Wool Socks, pair', 1000, 1000], ['/cdn/storefront/wool-socks-rib.jpg', 'Wool Socks, rib', 1000, 1000]],
                'metafields' => ['care' => 'Wash cool, dry flat', 'material' => 'Merino wool blend'],
            ],
            [
                'handle' => 'market-basket', 'title' => 'Market Basket', 'vendor' => 'Field Goods', 'type' => 'Home',
                'description' => 'A woven basket with leather handles, sized for a week of vegetables.',
                'price' => 4300, 'compare_at' => 5000, 'badges' => ['home', 'limited'],
                'variants' => [['market-basket-small', 'Small', 'Small', 4300, true], ['market-basket-medium', 'Medium', 'Medium', 4700, true], ['market-basket-large', 'Large', 'Large', 5200, false]],
                'images' => [['/cdn/storefront/market-basket-front.jpg', 'Market Basket, front', 1200, 1200], ['/cdn/storefront/market-basket-handles.jpg', 'Market Basket, handles', 1200, 1200]],
                'metafields' => ['care' => 'Keep out of direct sun', 'material' => 'Seagrass, leather'],
            ],
            [
                'handle' => 'notebook-set', 'title' => 'Notebook Set', 'vendor' => 'Paper Mill', 'type' => 'Desk',
                'description' => 'Three saddle-stitched notebooks on paper that takes fountain ink without bleeding.',
                'price' => 1900, 'compare_at' => 1900, 'badges' => ['desk', 'new'],
                'variants' => [['notebook-set-ruled', 'Ruled', 'Ruled', 1900, true], ['notebook-set-dotted', 'Dotted', 'Dotted', 1900, true], ['notebook-set-plain', 'Plain', 'Plain', 1900, true]],
                'images' => [['/cdn/storefront/notebook-set-stack.jpg', 'Notebook Set, stack', 1100, 900], ['/cdn/storefront/notebook-set-open.jpg', 'Notebook Set, open', 1100, 900]],
                'metafields' => ['care' => 'Keep away from damp', 'material' => '100gsm uncoated paper'],
            ],
            [
                'handle' => 'silk-scarf', 'title' => 'Silk Scarf', 'vendor' => 'Atelier No. 8', 'type' => 'Accessories',
                'description' => 'A hand-rolled square in heavy silk twill, printed with a coastal map motif.',
                'price' => 7400, 'compare_at' => 8900, 'badges' => ['gift', 'sale'],
                'variants' => [['silk-scarf-tide', 'Tide', 'Tide', 7400, true], ['silk-scarf-dune', 'Dune', 'Dune', 7400, false], ['silk-scarf-harbour', 'Harbour', 'Harbour', 7800, true]],
                'images' => [['/cdn/storefront/silk-scarf-flat.jpg', 'Silk Scarf, flat', 1200, 1200], ['/cdn/storefront/silk-scarf-tied.jpg', 'Silk Scarf, tied', 1200, 1200]],
                'metafields' => ['care' => 'Dry clean only', 'material' => '100% silk twill'],
            ],
            [
                'handle' => 'beach-towel', 'title' => 'Beach Towel', 'vendor' => 'Coastline', 'type' => 'Home',
                'description' => 'A flat-woven towel that dries quickly and packs down to nothing.',
                'price' => 3600, 'compare_at' => 3600, 'badges' => ['summer', 'travel'],
                'variants' => [['beach-towel-stripe', 'Stripe', 'Stripe', 3600, true], ['beach-towel-solid', 'Solid', 'Solid', 3600, true], ['beach-towel-check', 'Check', 'Check', 3800, true]],
                'images' => [['/cdn/storefront/beach-towel-folded.jpg', 'Beach Towel, folded', 1300, 900], ['/cdn/storefront/beach-towel-edge.jpg', 'Beach Towel, edge', 1300, 900]],
                'metafields' => ['care' => 'Wash before first use', 'material' => 'Turkish cotton'],
            ],
            [
                'handle' => 'glass-vase', 'title' => 'Glass Vase', 'vendor' => 'Studio Form', 'type' => 'Home',
                'description' => 'A hand-blown vase with a heavy base and a neck sized for a single stem.',
                'price' => 4600, 'compare_at' => 5200, 'badges' => ['home', 'limited'],
                'variants' => [['glass-vase-clear', 'Clear', 'Clear', 4600, true], ['glass-vase-smoke', 'Smoke', 'Smoke', 4900, true], ['glass-vase-green', 'Green', 'Green', 4900, false]],
                'images' => [['/cdn/storefront/glass-vase-front.jpg', 'Glass Vase, front', 1000, 1300], ['/cdn/storefront/glass-vase-base.jpg', 'Glass Vase, base', 1000, 1300]],
                'metafields' => ['care' => 'Hand wash', 'material' => 'Hand-blown glass'],
            ],
            [
                'handle' => 'knit-cap', 'title' => 'Knit Cap', 'vendor' => 'North Loom', 'type' => 'Hats',
                'description' => 'A close-fitting lambswool cap with a turned brim that sits below the ears.',
                'price' => 3100, 'compare_at' => 3100, 'badges' => ['warm', 'new'],
                'variants' => [['knit-cap-charcoal', 'Charcoal', 'Charcoal', 3100, true], ['knit-cap-oat', 'Oat', 'Oat', 3100, true], ['knit-cap-moss', 'Moss', 'Moss', 3100, false]],
                'images' => [['/cdn/storefront/knit-cap-front.jpg', 'Knit Cap, front', 1000, 1000], ['/cdn/storefront/knit-cap-brim.jpg', 'Knit Cap, brim', 1000, 1000]],
                'metafields' => ['care' => 'Hand wash cool', 'material' => 'Lambswool'],
            ],
            [
                'handle' => 'key-organizer', 'title' => 'Key Organizer', 'vendor' => 'Atelier No. 8', 'type' => 'Accessories',
                'description' => 'Holds six keys flat and silent, on a leather strap with a brass rivet.',
                'price' => 4100, 'compare_at' => 4800, 'badges' => ['gift', 'travel'],
                'variants' => [['key-organizer-brass', 'Brass', 'Brass', 4100, true], ['key-organizer-steel', 'Steel', 'Steel', 4100, true], ['key-organizer-black', 'Black', 'Black', 4300, true]],
                'images' => [['/cdn/storefront/key-organizer-front.jpg', 'Key Organizer, front', 900, 900], ['/cdn/storefront/key-organizer-open.jpg', 'Key Organizer, open', 900, 900]],
                'metafields' => ['care' => 'Tighten the rivet yearly', 'material' => 'Brass, bridle leather'],
            ],
            [
                'handle' => 'picnic-blanket', 'title' => 'Picnic Blanket', 'vendor' => 'Field Goods', 'type' => 'Home',
                'description' => 'Wool on top, waxed cotton beneath, with straps for carrying it over a shoulder.',
                'price' => 9200, 'compare_at' => 11200, 'badges' => ['summer', 'home'],
                'variants' => [['picnic-blanket-check', 'Check', 'Check', 9200, true], ['picnic-blanket-stripe', 'Stripe', 'Stripe', 9200, false], ['picnic-blanket-plain', 'Plain', 'Plain', 9600, true]],
                'images' => [['/cdn/storefront/picnic-blanket-rolled.jpg', 'Picnic Blanket, rolled', 1300, 1000], ['/cdn/storefront/picnic-blanket-spread.jpg', 'Picnic Blanket, spread', 1300, 1000]],
                'metafields' => ['care' => 'Brush clean, air dry', 'material' => 'Wool, waxed cotton'],
            ],
            [
                'handle' => 'tea-canister', 'title' => 'Tea Canister', 'vendor' => 'Paper Mill', 'type' => 'Kitchen',
                'description' => 'An airtight tin with a double lid that keeps loose leaf dry for months.',
                'price' => 2700, 'compare_at' => 2700, 'badges' => ['kitchen', 'desk'],
                'variants' => [['tea-canister-small', 'Small', 'Small', 2700, true], ['tea-canister-large', 'Large', 'Large', 3200, true], ['tea-canister-set', 'Set of two', 'Set', 5600, true]],
                'images' => [['/cdn/storefront/tea-canister-front.jpg', 'Tea Canister, front', 1000, 1200], ['/cdn/storefront/tea-canister-lid.jpg', 'Tea Canister, lid', 1000, 1200]],
                'metafields' => ['care' => 'Wipe dry before refilling', 'material' => 'Tinplate steel'],
            ],
            [
                'handle' => 'camp-lantern', 'title' => 'Camp Lantern', 'vendor' => 'Coastline', 'type' => 'Lighting',
                'description' => 'A rechargeable lantern with a warm low setting that runs for forty hours.',
                'price' => 5400, 'compare_at' => 6200, 'badges' => ['travel', 'limited'],
                'variants' => [['camp-lantern-sand', 'Sand', 'Sand', 5400, true], ['camp-lantern-slate', 'Slate', 'Slate', 5400, true], ['camp-lantern-red', 'Red', 'Red', 5600, false]],
                'images' => [['/cdn/storefront/camp-lantern-lit.jpg', 'Camp Lantern, lit', 1000, 1200], ['/cdn/storefront/camp-lantern-handle.jpg', 'Camp Lantern, handle', 1000, 1200]],
                'metafields' => ['care' => 'Charge every six months', 'material' => 'Aluminium, silicone'],
            ],
            [
                'handle' => 'table-clock', 'title' => 'Table Clock', 'vendor' => 'Studio Form', 'type' => 'Home',
                'description' => 'A silent-sweep clock with a brushed face and a folded steel stand.',
                'price' => 5700, 'compare_at' => 5700, 'badges' => ['home', 'desk'],
                'variants' => [['table-clock-steel', 'Steel', 'Steel', 5700, true], ['table-clock-black', 'Black', 'Black', 5700, true], ['table-clock-brass', 'Brass', 'Brass', 6100, true]],
                'images' => [['/cdn/storefront/table-clock-front.jpg', 'Table Clock, front', 1100, 1100], ['/cdn/storefront/table-clock-side.jpg', 'Table Clock, side', 1100, 1100]],
                'metafields' => ['care' => 'One AA battery, not included', 'material' => 'Brushed steel'],
            ],
            [
                'handle' => 'cashmere-wrap', 'title' => 'Cashmere Wrap', 'vendor' => 'North Loom', 'type' => 'Accessories',
                'description' => 'A generous wrap in two-ply cashmere, light enough to fold into a bag.',
                'price' => 12800, 'compare_at' => 14900, 'badges' => ['warm', 'gift'],
                'variants' => [['cashmere-wrap-pearl', 'Pearl', 'Pearl', 12800, true], ['cashmere-wrap-slate', 'Slate', 'Slate', 12800, false], ['cashmere-wrap-camel', 'Camel', 'Camel', 13400, true]],
                'images' => [['/cdn/storefront/cashmere-wrap-folded.jpg', 'Cashmere Wrap, folded', 1200, 1000], ['/cdn/storefront/cashmere-wrap-worn.jpg', 'Cashmere Wrap, worn', 1200, 1000]],
                'metafields' => ['care' => 'Hand wash, dry flat', 'material' => 'Two-ply cashmere'],
            ],
            [
                'handle' => 'card-holder', 'title' => 'Card Holder', 'vendor' => 'Atelier No. 8', 'type' => 'Accessories',
                'description' => 'A slim sleeve for four cards and a folded note, no stitching on the spine.',
                'price' => 3300, 'compare_at' => 3300, 'badges' => ['gift', 'new'],
                'variants' => [['card-holder-black', 'Black', 'Black', 3300, true], ['card-holder-tan', 'Tan', 'Tan', 3300, true], ['card-holder-olive', 'Olive', 'Olive', 3500, true]],
                'images' => [['/cdn/storefront/card-holder-front.jpg', 'Card Holder, front', 900, 700], ['/cdn/storefront/card-holder-side.jpg', 'Card Holder, side', 900, 700]],
                'metafields' => ['care' => 'Keep dry', 'material' => 'Vegetable-tanned leather'],
            ],
            [
                'handle' => 'sketchbook', 'title' => 'Sketchbook', 'vendor' => 'Paper Mill', 'type' => 'Desk',
                'description' => 'A lay-flat sketchbook with heavy sized paper that takes a wash without buckling.',
                'price' => 2500, 'compare_at' => 2500, 'badges' => ['desk', 'travel'],
                'variants' => [['sketchbook-a5', 'A5', 'A5', 2500, true], ['sketchbook-a4', 'A4', 'A4', 3400, true], ['sketchbook-square', 'Square', 'Square', 2900, false]],
                'images' => [['/cdn/storefront/sketchbook-cover.jpg', 'Sketchbook, cover', 1000, 1300], ['/cdn/storefront/sketchbook-open.jpg', 'Sketchbook, open', 1000, 1300]],
                'metafields' => ['care' => 'Store flat', 'material' => '200gsm sized paper'],
            ],
        ];
    }

    /**
     * @var list<array{title: string, url: string}>
     */
    private const MAIN_MENU = [
        ['title' => 'New arrivals', 'url' => '/collections/new'],
        ['title' => 'Summer', 'url' => '/collections/summer-essentials'],
        ['title' => 'Travel', 'url' => '/collections/travel'],
        ['title' => 'Home', 'url' => '/collections/home'],
        ['title' => 'Journal', 'url' => '/blogs/journal'],
    ];

    /**
     * @var list<array{title: string, url: string}>
     */
    private const FOOTER_MENU = [
        ['title' => 'Shipping', 'url' => '/pages/shipping'],
        ['title' => 'Returns', 'url' => '/pages/returns'],
        ['title' => 'Sizing', 'url' => '/pages/sizing'],
        ['title' => 'Contact', 'url' => '/pages/contact'],
        ['title' => 'Stockists', 'url' => '/pages/stockists'],
    ];

    /**
     * @var list<array{handle: string, title: string, excerpt: string, author: string, published_at: string, reading_minutes: int, tags: list<string>, image: array{string, string, int, int}}>
     */
    private const ARTICLES = [
        [
            'handle' => 'packing-light', 'title' => 'Packing light for a weekend away',
            'excerpt' => 'Three small choices that make a two-night trip feel lighter than it is.',
            'author' => 'Ada Fenn', 'published_at' => '2026-05-14', 'reading_minutes' => 4,
            'tags' => ['travel', 'guides'],
            'image' => ['/cdn/storefront/journal-packing.jpg', 'A packed weekend bag', 1400, 900],
        ],
        [
            'handle' => 'choosing-a-daily-carry', 'title' => 'How to choose a daily carry',
            'excerpt' => 'A practical guide to materials, proportions and the weight you stop noticing.',
            'author' => 'Ruben Oyelaran', 'published_at' => '2026-04-28', 'reading_minutes' => 6,
            'tags' => ['bags', 'guides'],
            'image' => ['/cdn/storefront/journal-carry.jpg', 'A tote on a bench', 1400, 900],
        ],
        [
            'handle' => 'objects-that-earn-their-place', 'title' => 'The objects that earn their place',
            'excerpt' => 'On buying less, using more, and the quiet test a good object has to pass.',
            'author' => 'Mira Delacroix', 'published_at' => '2026-04-02', 'reading_minutes' => 5,
            'tags' => ['essays'],
            'image' => ['/cdn/storefront/journal-objects.jpg', 'A shelf of everyday objects', 1400, 900],
        ],
    ];

    public static function shop(): ShopDrop
    {
        return new ShopDrop(
            name: 'Northstar Goods',
            domain: 'northstar-goods.example',
            currency: 'EUR',
            description: 'Useful objects for slower days, closer places and longer weekends.',
        );
    }

    /**
     * Totals are stored, not summed from line items — see the class docblock.
     */
    public static function cart(): CartDrop
    {
        return new CartDrop(itemCount: 3, totalPrice: 12650);
    }

    /**
     * @return list<LinkDrop>
     */
    public static function mainMenu(): array
    {
        return self::links(self::MAIN_MENU);
    }

    /**
     * @return list<LinkDrop>
     */
    public static function footerMenu(): array
    {
        return self::links(self::FOOTER_MENU);
    }

    /**
     * @return non-empty-list<ProductDrop>
     */
    public static function products(): array
    {
        return array_map(self::productFromRow(...), self::productRows());
    }

    /**
     * @param  array{
     *     handle: string,
     *     title: string,
     *     vendor: string,
     *     type: string,
     *     description: string,
     *     price: int,
     *     compare_at: int,
     *     badges: list<string>,
     *     variants: non-empty-list<array{string, string, string, int, bool}>,
     *     images: non-empty-list<array{string, string, int, int}>,
     *     metafields: array<string, string>
     * }  $row
     */
    private static function productFromRow(array $row): ProductDrop
    {
        return new ProductDrop(
            handle: $row['handle'],
            title: $row['title'],
            vendor: $row['vendor'],
            type: $row['type'],
            description: $row['description'],
            priceCents: $row['price'],
            compareAtPriceCents: $row['compare_at'],
            badges: $row['badges'],
            images: array_map(self::image(...), $row['images']),
            variants: array_map(self::variant(...), $row['variants']),
            metafields: new MetafieldsDrop($row['metafields']),
        );
    }

    /**
     * @param  array{string, string, string, int, bool}  $variant
     */
    private static function variant(array $variant): VariantDrop
    {
        return new VariantDrop(
            id: $variant[0],
            title: $variant[1],
            option: $variant[2],
            priceCents: $variant[3],
            available: $variant[4],
        );
    }

    public static function collection(): CollectionDrop
    {
        return new CollectionDrop(
            handle: 'summer-essentials',
            title: 'Summer Essentials',
            label: 'summer',
            description: 'Everyday pieces for long weekends, slow mornings and bright afternoons.',
            tags: ['New arrivals', 'Travel ready', 'Summer layers', 'Gifts under 100', 'Last few'],
            products: self::products(),
            image: self::image(['/cdn/storefront/collection-summer.jpg', 'Summer Essentials', 1600, 900]),
        );
    }

    /**
     * The product page subject. The row index is hard-wired rather than searched,
     * and only that row is built — returning products()[4] would construct all 24
     * products, and that cost lands inside the measured region.
     */
    public static function product(): ProductDrop
    {
        return self::productFromRow(self::productRows()[4]);
    }

    public static function page(): PageDrop
    {
        return new PageDrop(
            handle: 'our-story',
            title: 'Our story',
            content: 'Northstar Goods began with a single canvas bag and a stubborn belief that most things are made too quickly.',
            sections: [
                ['title' => 'How we choose makers', 'body' => 'We visit every workshop before we place an order, and we place small orders first.'],
                ['title' => 'What we will not do', 'body' => 'No seasonal churn, no synthetic linings, and no product we would not use ourselves.'],
                ['title' => 'Repairs', 'body' => 'Send anything back within five years and we will repair it or replace it.'],
            ],
        );
    }

    /**
     * @return list<ArticleDrop>
     */
    public static function articles(): array
    {
        $articles = [];

        foreach (self::ARTICLES as $article) {
            $articles[] = new ArticleDrop(
                handle: $article['handle'],
                title: $article['title'],
                excerpt: $article['excerpt'],
                author: $article['author'],
                publishedAt: $article['published_at'],
                readingMinutes: $article['reading_minutes'],
                tags: $article['tags'],
                image: self::image($article['image']),
            );
        }

        return $articles;
    }

    /**
     * @param  list<array{title: string, url: string}>  $links
     * @return list<LinkDrop>
     */
    private static function links(array $links): array
    {
        $drops = [];

        foreach ($links as $link) {
            $drops[] = new LinkDrop(title: $link['title'], url: $link['url']);
        }

        return $drops;
    }

    /**
     * @param  array{string, string, int, int}  $image
     */
    private static function image(array $image): ImageDrop
    {
        return new ImageDrop(src: $image[0], alt: $image[1], width: $image[2], height: $image[3]);
    }
}
