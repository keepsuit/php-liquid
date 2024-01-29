<?php

namespace Keepsuit\Liquid\Performance\Shopify;

use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\TagParseContext;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\TagBlock;

class CommentFormTag extends TagBlock
{
    protected string $variableName;

    protected array $attributes;

    protected BodyNode $body;

    public static function tagName(): string
    {
        return 'form';
    }

    public function parse(TagParseContext $context): static
    {
        assert($context->body !== null);
        $this->body = $context->body;

        $variableName = $context->params->expression();
        $this->variableName = match (true) {
            $variableName instanceof VariableLookup, is_string($variableName) => (string) $variableName,
            default => throw new SyntaxException('Invalid variable name'),
        };

        $this->attributes = [];

        $context->params->assertEnd();

        return $this;
    }

    public function render(RenderContext $context): string
    {
        $article = $context->get($this->variableName);
        assert(is_array($article));

        $context->stack(function (RenderContext $context) {
            $context->set('form', [
                'posted_successfully?' => $context->getRegister('posted_successfully'),
                'errors' => $context->get('comment.errors'),
                'author' => $context->get('comment.author'),
                'email' => $context->get('comment.email'),
                'body' => $context->get('comment.body'),
            ]);
        });

        return $this->wrapInForm($article, $this->body->render($context));
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
