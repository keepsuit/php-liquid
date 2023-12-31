<?php

namespace Keepsuit\Liquid\Parse;

use Closure;
use Keepsuit\Liquid\Exceptions\SyntaxException;
use Keepsuit\Liquid\Nodes\BlockBodySection;
use Keepsuit\Liquid\Nodes\BlockBodySectionDelimiter;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Tag;

final class BlockParser
{
    protected const LIQUID_TAG_TOKEN = '/\A\s*('.Regex::TagName.')\s*(.*?)\z/';

    protected const FULL_TOKEN = Regex::FullTagToken;

    protected const CONTENT_OF_VARIABLE = '/\A'.Regex::VariableStart.Regex::WhitespaceControl.'?((\S|\s)*?)'.Regex::WhitespaceControl.'?'.Regex::VariableEnd.'\z/m';

    protected const WHITESPACE_OR_NOTHING = '/\A\s*\z/';

    protected const TAGSTART = '{%';

    protected const VARSTART = '{{';

    protected ?Closure $subTagsHandler = null;

    public function __construct(
        protected ?string $tagName = null,
        protected ?string $markup = null
    ) {
    }

    public static function forTag(string $tagName, string $markup): static
    {
        return new self($tagName, $markup);
    }

    public static function forDocument(): static
    {
        return new self();
    }

    public function subTagsHandler(?Closure $subTagsHandler): static
    {
        $this->subTagsHandler = $subTagsHandler;

        return $this;
    }

    /**
     * @return array<BlockBodySection>
     *
     * @throws SyntaxException
     */
    public function parse(Tokenizer $tokenizer, ParseContext $parseContext): array
    {
        $parseContext->lineNumber = $tokenizer->getStartLineNumber();

        if ($tokenizer->forLiquidTag) {
            return $this->parseForLiquidTag($tokenizer, $parseContext);
        }

        return $this->parseForDocument($tokenizer, $parseContext);
    }

    protected function endTag(): ?string
    {
        if ($this->tagName === null) {
            return null;
        }

        return 'end'.$this->tagName;
    }

    /**
     * @return array<BlockBodySection>
     *
     * @throws SyntaxException
     */
    protected function parseForDocument(Tokenizer $tokenizer, ParseContext $parseContext): array
    {
        $sections = [];
        $section = new BlockBodySection(
            start: $this->tagName ? new BlockBodySectionDelimiter($this->tagName, $this->markup ?? '') : null,
            end: null,
        );
        $sections[] = $section;

        foreach ($tokenizer->shift() as $token) {
            if ($token === '') {
                continue;
            }

            if (str_starts_with($token, self::TAGSTART)) {
                $section->setNodeList(self::whitespaceHandler($token, $parseContext, $section->nodeList()));

                if (preg_match(self::FULL_TOKEN, $token, $matches) !== 1) {
                    $this->handleInvalidTagToken($token, $parseContext);

                    continue;
                }

                $tagName = $matches[2];
                $markup = $matches[4];

                if ($parseContext->lineNumber !== null) {
                    $parseContext->lineNumber += substr_count($matches[1], PHP_EOL) + substr_count($matches[3], PHP_EOL);
                }

                /** @var class-string<Tag>|null $tagClass */
                $tagClass = $parseContext->tagRegistry->get($tagName) ?? null;

                if ($tagClass !== null) {
                    $tag = (new $tagClass($markup, $parseContext->lineNumber));
                    $tag->parse($parseContext, $tokenizer);
                    $section->pushNode($tag);

                    continue;
                }

                if ($this->isBlockEndTag($tagName)) {
                    return $sections;
                }

                $this->handleUnknownTag($tagName, $parseContext);

                $section->setEnd(new BlockBodySectionDelimiter($tagName, $markup));

                $section = new BlockBodySection(
                    start: $section->endDelimiter(),
                );
                $sections[] = $section;
            } elseif (str_starts_with($token, self::VARSTART)) {
                $section->setNodeList(static::whitespaceHandler($token, $parseContext, $section->nodeList()));
                $section->pushNode(static::createVariable($token, $parseContext));
            } else {
                if ($parseContext->trimWhitespace) {
                    $token = ltrim($token);
                }
                $parseContext->trimWhitespace = false;
                $section->pushNode($token);
            }

            $parseContext->lineNumber = $tokenizer->getEndLineNumber();
        }

        if ($section->endDelimiter() === null && $this->endTag() !== null) {
            throw SyntaxException::tagNeverClosed($this->tagName, $parseContext);
        }

        return $sections;
    }

    /**
     * @return array<BlockBodySection>
     *
     * @throws SyntaxException
     */
    protected function parseForLiquidTag(Tokenizer $tokenizer, ParseContext $parseContext): array
    {
        $sections = [];
        $section = new BlockBodySection(
            start: $this->tagName ? new BlockBodySectionDelimiter($this->tagName, $this->markup ?? '') : null,
            end: null,
        );
        $sections[] = $section;

        foreach ($tokenizer->shift() as $token) {
            if ($token === '' || preg_match(self::WHITESPACE_OR_NOTHING, $token) === 1) {
                $parseContext->lineNumber = $tokenizer->getEndLineNumber();

                continue;
            }

            if (preg_match(self::LIQUID_TAG_TOKEN, $token, $matches) !== 1) {
                throw SyntaxException::unknownTag($parseContext, $token, 'liquid');
            }

            $tagName = $matches[1];
            $markup = $matches[2];

            /** @var class-string<Tag>|null $tagClass */
            $tagClass = $parseContext->tagRegistry->get($tagName) ?? null;

            if ($tagClass !== null) {
                $tag = (new $tagClass($markup, $parseContext->lineNumber));
                $tag->parse($parseContext, $tokenizer);
                $section->pushNode($tag);

                continue;
            }

            if ($this->isBlockEndTag($tagName)) {
                return $sections;
            }

            $this->handleUnknownTag($tagName, $parseContext, forLiquid: true);

            $section->setEnd(new BlockBodySectionDelimiter($tagName, $markup));

            $section = new BlockBodySection(
                start: $section->endDelimiter(),
            );
            $sections[] = $section;

            $parseContext->lineNumber = $tokenizer->getEndLineNumber();
        }

        if ($section->endDelimiter() === null && $this->endTag() !== null) {
            $parseContext->lineNumber = $tokenizer->getEndLineNumber() - 1;
            throw SyntaxException::tagNeverClosed($this->tagName, $parseContext);
        }

        return $sections;
    }

    protected static function whitespaceHandler(string $token, ParseContext $parseContext, array $nodeList): array
    {
        if (strlen($token) < 3) {
            return $nodeList;
        }

        if ($token[2] === Regex::WhitespaceControl) {
            $previousToken = $nodeList[count($nodeList) - 1] ?? null;

            if (is_string($previousToken)) {
                $nodeList[count($nodeList) - 1] = rtrim($previousToken);
            }
        }

        $parseContext->trimWhitespace = $token[strlen($token) - 3] === Regex::WhitespaceControl;

        return $nodeList;
    }

    protected static function createVariable(string $token, ParseContext $parseContext): Variable
    {
        if (preg_match(static::CONTENT_OF_VARIABLE, $token, $matches) === 1) {
            return Variable::fromMarkup($matches[1], $parseContext->lineNumber);
        }

        throw SyntaxException::missingVariableTerminator($token, $parseContext);
    }

    protected function handleInvalidTagToken(string $token, ParseContext $parseContext): void
    {
        if (str_ends_with($token, '%}')) {
            $this->handleUnknownTag($token, $parseContext);

            return;
        }

        throw SyntaxException::missingTagTerminator($token, $parseContext);
    }

    /**
     * @throws SyntaxException
     */
    protected function handleUnknownTag(string $tagName, ParseContext $parseContext, bool $forLiquid = false): void
    {
        $handler = $this->subTagsHandler;

        if ($handler !== null && $handler($tagName)) {
            return;
        }

        if ($forLiquid) {
            throw SyntaxException::unknownTag($parseContext, $tagName, blockTagName: 'liquid', blockDelimiter: '%}');
        }

        throw SyntaxException::unknownTag($parseContext, $tagName, $this->tagName ?? '');
    }

    protected function isBlockEndTag(string $tagName): bool
    {
        return $tagName === $this->endTag();
    }
}
