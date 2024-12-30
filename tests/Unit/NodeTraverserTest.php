<?php

use Keepsuit\Liquid\Contracts\NodeVisitor;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;

test('traverse node tree', function () {
    $template = parseTemplate('{% for x in xs offset: test %}{{ other }}{% endfor %}');

    $traverser = (new \Keepsuit\Liquid\Parse\NodeTraverser)
        ->addVisitor($visitor = new \Keepsuit\Liquid\Tests\Stubs\StubNodeVisitor);

    $traverser->traverse($template->root);

    expect($visitor->getNodes())
        ->toHaveCount(5)
        ->{0}->toBeInstanceOf(\Keepsuit\Liquid\Nodes\Variable::class)
        ->{1}->toBeInstanceOf(\Keepsuit\Liquid\Nodes\BodyNode::class)
        ->{2}->toBeInstanceOf(\Keepsuit\Liquid\Tags\ForTag::class)
        ->{3}->toBeInstanceOf(\Keepsuit\Liquid\Nodes\BodyNode::class)
        ->{4}->toBeInstanceOf(\Keepsuit\Liquid\Nodes\Document::class);
});

test('node visitor can replace body node children', function () {
    $template = parseTemplate('{{ var1 }} {{ var2 }}');

    $traverser = (new \Keepsuit\Liquid\Parse\NodeTraverser)
        ->addVisitor(new class implements NodeVisitor
        {
            public function enterNode(\Keepsuit\Liquid\Nodes\Node $node): void {}

            public function leaveNode(\Keepsuit\Liquid\Nodes\Node $node): void
            {
                if ($node instanceof \Keepsuit\Liquid\Nodes\BodyNode) {
                    $node->setChildren([
                        new \Keepsuit\Liquid\Nodes\Text('replaced'),
                    ]);
                }
            }
        });

    $traverser->traverse($template->root);

    expect($template->root->body->children())
        ->toHaveCount(1)
        ->{0}->toBeInstanceOf(\Keepsuit\Liquid\Nodes\Text::class);

    expect($template->render(new \Keepsuit\Liquid\Render\RenderContext))->toBe('replaced');
});

test('variable', function () {
    expect(buildNodeTree('{{ test }}'))->toBe([
        [
            'document',
            null,
            [
                ['variable', 'test'],
            ],
        ],
    ]);
});

test('tag', function () {
    expect(buildNodeTree('{% if test %}{% endif %}'))->toBe([
        [
            'document',
            null,
            [
                ['tag', 'if'],
            ],
        ],
    ]);
});

test('tag with body', function () {
    expect(buildNodeTree('{% if 1 == 1 %}{{ test }}{% endif %}'))->toBe([
        [
            'document',
            null,
            [
                [
                    'tag',
                    'if',
                    [
                        ['variable', 'test'],
                    ],
                ],
            ],
        ],
    ]);
});

test('render tag', function () {
    expect(buildNodeTree('{% render "hai" %}'))->toBe([
        [
            'document',
            null,
            [
                ['tag', 'render'],
            ],
        ],
        [
            'document',
            null,
            [
                ['variable', 'hai'],
            ],
        ],
    ]);
});

function buildNodeTree(string $source): array
{
    $visitor = new \Keepsuit\Liquid\Tests\Stubs\NodeTreeVisitor;

    $environment = \Keepsuit\Liquid\EnvironmentFactory::new()
        ->setFilesystem(new StubFileSystem(['hai' => '{{ hai }}']))
        ->addExtension(new class($visitor) extends \Keepsuit\Liquid\Extensions\Extension
        {
            public function __construct(protected NodeVisitor $nodeVisitor) {}

            public function getNodeVisitors(): array
            {
                return [
                    $this->nodeVisitor,
                ];
            }
        })
        ->build();

    $template = $environment->parseString($source);

    return \Keepsuit\Liquid\Support\Arr::map(
        $visitor->getTrees(),
        fn (\Keepsuit\Liquid\Tests\Stubs\NodeTreeItem $item) => $item->serialize()
    );
}
