<?php

namespace Keepsuit\Liquid\Performance\Shopify;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Regex;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\TagBlock;

class CommentFormTag extends TagBlock
{
    protected const Syntax = '/('.Regex::VariableSignature.'+)/';

    protected string $variableName;

    protected array $attributes;

    public static function tagName(): string
    {
        return 'form';
    }

    public function parse(ParseContext $parseContext, Tokenizer $tokenizer): static
    {
        parent::parse($parseContext, $tokenizer);

        if (preg_match(self::Syntax, $this->markup, $matches)) {
            $this->variableName = $matches[1];
            $this->attributes = [];
        } else {
            throw new SyntaxException("Syntax Error in 'comment_form' - Valid syntax: comment_form [article]");
        }

        return $this;
    }

    public function render(Context $context): string
    {
        $article = $context->get($this->variableName);
        assert(is_array($article));

        $context->stack(function (Context $context) {
            $context->set('form', [
                'posted_successfully?' => $context->getRegister('posted_successfully'),
                'errors' => $context->get('comment.errors'),
                'author' => $context->get('comment.author'),
                'email' => $context->get('comment.email'),
                'body' => $context->get('comment.body'),
            ]);
        });

        return $this->wrapInForm($article, parent::render($context));
    }

    protected function wrapInForm(array $article, string $input): string
    {
        return <<<HTML
        <form id="article-{$article['id']}-comment-form" class="comment-form" method="post" action="">
        $input
        </form>
        HTML;
    }
}
