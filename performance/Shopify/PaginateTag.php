<?php

namespace Keepsuit\Liquid\Performance\Shopify;

use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Range;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Parse\TokenType;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

class PaginateTag extends TagBlock
{
    protected VariableLookup|string $collectionName;

    protected int $pageSize = 20;

    protected array $attributes;

    protected BodyNode $body;

    public static function tagName(): string
    {
        return 'paginate';
    }

    public function parse(TagParseContext $context): static
    {
        assert($context->body !== null);
        $this->body = $context->body;

        $collectionName = $context->params->expression();
        $this->collectionName = match (true) {
            $collectionName instanceof VariableLookup, is_string($collectionName) => (string) $collectionName,
            default => throw new SyntaxException('Invalid collection name'),
        };

        if ($context->params->idOrFalse('by')) {
            $this->pageSize = (int) $context->params->consume(TokenType::Number)->data;
        }

        if (! $context->params->isEnd()) {
            dd('paginate', $context->params->current());
        }

        $context->params->assertEnd();

        return $this;
    }

    public function render(RenderContext $context): string
    {
        return $context->stack(function (RenderContext $context) {
            $currentPage = $context->get('current_page');

            $collection = $context->get($this->collectionName);
            $collection = match (true) {
                $collection instanceof Range => $collection->toArray(),
                $collection instanceof \Iterator => iterator_to_array($collection),
                default => $collection,
            };
            if (! is_array($collection)) {
                throw new InvalidArgumentException(sprintf('Cannot paginate array %s. Not found.', $this->collectionName));
            }

            $collectionSize = count($collection);
            $pageCount = ceil($collectionSize / $this->pageSize) + 1;

            $pagination = [
                'page_size' => $this->pageSize,
                'current_page' => 5,
                'current_offset' => $this->pageSize * 5,
                'items' => $collectionSize,
                'pages' => $pageCount - 1,
                'previous' => $currentPage > 1 ? $this->link('&laquo; Previous', $currentPage - 1) : null,
                'next' => $currentPage < $pageCount - 1 ? $this->link('Next &raquo;', $currentPage + 1) : null,
                'parts' => [],
            ];

            if ($pageCount > 2) {
                foreach (range(1, (int) $pageCount - 1) as $page) {
                    $pagination['parts'][] = match (true) {
                        $currentPage === $page => $this->noLink((string) $page),
                        $page === 1 => $this->link((string) $page, $page),
                        $page === $pageCount - 1 => $this->link((string) $page, $page),
                        $page <= ($currentPage - (int) $this->attributes['window_size']) => $this->link('...', $page),
                        $page >= ($currentPage + (int) $this->attributes['window_size']) => $this->link('...', $page),
                        default => $this->link((string) $page, $page),
                    };
                }
            }

            $context->set('paginate', $pagination);

            return $this->body->render($context);
        });
    }

    protected function noLink(string $title): array
    {
        return [
            'title' => $title,
            'is_link' => false,
        ];
    }

    protected function link(string $title, int $page): array
    {
        return [
            'title' => $title,
            'url' => $this->currentUrl().'?page='.$page,
            'is_link' => true,
        ];
    }

    protected function currentUrl(): string
    {
        return '/collections/frontpage';
    }
}
