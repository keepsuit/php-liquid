<?php

namespace Keepsuit\Liquid\Nodes;

use Generator;
use Keepsuit\Liquid\Contracts\CanBeRendered;
use Keepsuit\Liquid\Exceptions\LiquidException;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Parse\BlockParser;
use Keepsuit\Liquid\Parse\ParseContext;
use Keepsuit\Liquid\Parse\Tokenizer;
use Keepsuit\Liquid\Render\Context;
use Keepsuit\Liquid\Support\GeneratorToString;
use Keepsuit\Liquid\Tag;
use Throwable;

class Document implements CanBeRendered
{
    use GeneratorToString;

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
        } catch (Throwable $exception) {
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
        return $this->generatorToString($this->renderAsync($context));
    }

    /**
     * @return Generator<string>
     *
     * @throws LiquidException
     */
    public function renderAsync(Context $context): Generator
    {
        if ($context->getProfiler() !== null) {
            yield $context->getProfiler()->profile($context->getTemplateName(), fn () => $this->renderBody($context));

            return;
        }

        yield from $this->renderBody($context);
    }

    /**
     * @return Generator<string>
     *
     * @throws LiquidException
     */
    protected function renderBody(Context $context): Generator
    {
        foreach ($this->body->renderAsync($context) as $output) {
            $context->resourceLimits->incrementWriteScore($output);
            yield $output;
        }
    }

    /**
     * @return array<Tag|Variable|string>
     */
    public function nodeList(): array
    {
        return $this->body->nodeList();
    }
}
