<?php

namespace Keepsuit\Liquid\Performance\Shopify;

use Keepsuit\Liquid\Exceptions\InvalidArgumentException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\TagBlock;

class PaginateTag extends TagBlock
{
    protected const Syntax = '/('.Regex::QuotedFragment.')\s*(by\s*(\d+))?/';

    protected string $collectionName;

    protected int $pageSize;

    protected array $attributes;

    public static function tagName(): string
    {
        return 'paginate';
    }

    public function parse(Tokenizer $tokenizer): static
    {
        parent::parse($tokenizer);

        if (preg_match(self::Syntax, $this->markup, $matches)) {
            $this->collectionName = $matches[1];
            $this->pageSize = isset($matches[2]) ? (int) $matches[3] : 20;
        } else {
            throw new SyntaxException("Syntax Error in tag 'paginate' - Valid syntax: paginate [collection] by number");
        }

        $this->attributes = ['window_size' => 3];
        preg_match_all(sprintf('/%s/', Regex::TagAttributes), $this->markup, $attributeMatches, PREG_SET_ORDER);
        foreach ($attributeMatches as $matches) {
            $this->attributes[$matches[1]] = $this->parseExpression($matches[2]);
        }

        return $this;
    }

    public function render(Context $context): string
    {
        return $context->stack(function (Context $context) {
            $currentPage = $context->get('current_page');

            $collection = $context->get($this->collectionName);
            $collection = is_iterable($collection) ? iterator_to_array($collection) : $collection;

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
                foreach (range(1, $pageCount - 1) as $page) {
                    $pagination['parts'][] = match (true) {
                        $currentPage === $page => $this->noLink($page),
                        $page === 1 => $this->link($page, $page),
                        $page === $pageCount - 1 => $this->link($page, $page),
                        $page <= $currentPage - $this->attributes['window_size'] => $this->link('...', $page),
                        $page >= $currentPage + $this->attributes['window_size'] => $this->link('...', $page),
                        default => $this->link($page, $page),
                    };
                }
            }

            $context->set('paginate', $pagination);

            return parent::render($context);
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
