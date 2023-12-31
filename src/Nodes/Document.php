<?php

namespace Keepsuit\Liquid\Nodes;

use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\BlockParser;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Tag;

class Document implements CanBeRendered
{
    public function __construct(
        protected BlockBodySection $body,
    ) {
    }

    /**
     * @throws LiquidException
     */
    public static function parse(ParseContext $parseContext, Tokenizer $tokenizer): Document
    {
        try {
            $bodySections = BlockParser::forDocument()->parse($tokenizer, $parseContext);
        } catch (SyntaxException $exception) {
            if (in_array($exception->tagName, ['else', 'end'])) {
                $exception = SyntaxException::unexpectedOuterTag($parseContext, $exception->tagName ?? '');
            }

            $parseContext->handleError($exception);
        } catch (\Throwable $exception) {
            $parseContext->handleError($exception);
        }

        return new Document(
            body: $bodySections[0] ?? new BlockBodySection()
        );
    }

    /**
     * @throws LiquidException
     */
    public function render(Context $context): string
    {
        if ($context->getProfiler() !== null) {
            return $context->getProfiler()->profile($context->getTemplateName(), fn () => $this->body->render($context));
        }

        return $this->body->render($context);
    }

    /**
     * @return array<Tag|Variable|string>
     */
    public function nodeList(): array
    {
        return $this->body->nodeList();
    }
}
