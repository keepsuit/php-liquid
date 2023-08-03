<?php

namespace Keepsuit\Liquid;

use Closure;

final class BlockParser
{
    protected const LIQUID_TAG_TOKEN = '/\A\s*('.Regex::TagName.')\s*(.*?)\z';

    protected const FULL_TOKEN = '/\A'.Regex::TagStart.Regex::WhitespaceControl.'?(\s*)('.Regex::TagName.')(\s*)(.*?)'.Regex::WhitespaceControl.'?'.Regex::TagEnd.'\z/m';

    protected const CONTENT_OF_VARIABLE = '/\A'.Regex::VariableStart.Regex::WhitespaceControl.'?(.*?)'.Regex::WhitespaceControl.'?'.Regex::VariableEnd.'\z/m';

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
        return new static();
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
        $parseContext->lineNumber = $tokenizer->getLineNumber();

        if ($tokenizer->forLiquidTag) {
            // TODO: Implement parseForLiquidTag
            throw new \RuntimeException('Parse for liquid tag not implemented yet');
            // return static::parseForLiquidTag($tokenizer, $parseContext);
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

        while (($token = $tokenizer->shift()) !== null) {
            if ($token === '') {
                continue;
            }

            if (str_starts_with($token, self::TAGSTART)) {
                $section->setNodeList(self::whitespaceHandler($token, $parseContext, $section->nodeList()));

                if (preg_match(self::FULL_TOKEN, $token, $matches) !== 1) {
                    static::handleInvalidTagToken($token, $parseContext);
                }

                $tagName = $matches[2];
                $markup = $matches[4];

                if ($parseContext->lineNumber !== null) {
                    $parseContext->lineNumber += substr_count($matches[1], PHP_EOL) + substr_count($matches[3], PHP_EOL);
                }

                if ($tagName === 'liquid') {
                    // TODO: Implement liquid tag
                    throw new \RuntimeException('Liquid tag not implemented yet');
                    //continue;
                }

                /** @var class-string<Tag>|null $tagClass */
                $tagClass = Template::registeredTags()[$tagName] ?? null;

                if ($tagClass !== null) {
                    $tag = (new $tagClass($markup, $parseContext))->parse($tokenizer);
                    $section->pushNode($tag);

                    continue;
                }

                if ($this->isBlockEndTag($tagName)) {
                    return $sections;
                }

                $this->handleUnknownTag($tagName, $markup, $parseContext);

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

            $parseContext->lineNumber = $tokenizer->getLineNumber();
        }

        return $sections;
    }

    /**
     * @return never-return
     */
    protected static function handleInvalidTagToken(string $token, ParseContext $parseContext): void
    {
        throw SyntaxException::missingTagTerminator($token, $parseContext);
    }

    protected static function whitespaceHandler(string $token, ParseContext $parseContext, array $nodeList): array
    {
        if ($token[2] === Regex::WhitespaceControl) {
            $previousToken = $nodeList[count($nodeList) - 1] ?? null;

            if (is_string($previousToken)) {
                $firstChar = $previousToken[0] ?? '';
                $previousToken = rtrim($previousToken);
                if ($previousToken === '') {
                    $previousToken .= $firstChar;
                }
                $nodeList[count($nodeList) - 1] = $previousToken;
            }
        }

        $parseContext->trimWhitespace = $token[strlen($token) - 3] === Regex::WhitespaceControl;

        return $nodeList;
    }

    protected static function createVariable(string $token, ParseContext $parseContext): Variable
    {
        if (preg_match(static::CONTENT_OF_VARIABLE, $token, $matches) === 1) {
            return new Variable($matches[1], $parseContext);
        }

        throw SyntaxException::missingVariableTerminator($token, $parseContext);
    }

    /**
     * @throws SyntaxException
     */
    protected function handleUnknownTag(string $tagName, string $markup, ParseContext $parseContext): void
    {
        $handler = $this->subTagsHandler;

        if ($handler !== null && $handler($tagName)) {
            return;
        }

        throw SyntaxException::unknownTag($parseContext, $tagName, $markup);
    }

    protected function isBlockEndTag(string $tagName): bool
    {
        return $tagName === $this->endTag();
    }
}
