<?php

namespace Keepsuit\Liquid;

use Closure;

class BlockBody
{
    protected const LIQUID_TAG_TOKEN = '/\A\s*('.Regex::TagName.')\s*(.*?)\z';

    protected const FULL_TOKEN = '/\A'.Regex::TagStart.Regex::WhitespaceControl.'?(\s*)('.Regex::TagName.')(\s*)(.*?)'.Regex::WhitespaceControl.'?'.Regex::TagEnd.'\z/m';

    protected const CONTENT_OF_VARIABLE = '/\A'.Regex::VariableStart.Regex::WhitespaceControl.'?(.*?)'.Regex::WhitespaceControl.'?'.Regex::VariableEnd.'\z/m';

    protected const WHITESPACE_OR_NOTHING = '/\A\s*\z/';

    protected const TAGSTART = '{%';

    protected const VARSTART = '{{';

    public function __construct(
        /** @var array<Tag|Variable|string> */
        protected array $nodeList = [],
        public readonly bool $blank = true
    ) {
    }

    /**
     * @return array<Tag|Variable|string>
     */
    public function nodeList(): array
    {
        return $this->nodeList;
    }

    public static function parse(Tokenizer $tokenizer, ParseContext $parseContext, Closure $unknownTagHandler = null): BlockBody
    {
        $parseContext->lineNumber = $tokenizer->getLineNumber();

        if ($tokenizer->forLiquidTag) {
            dd('forLiquidTag');
            //            return static::parseForLiquidTag($tokenizer, $parseContext);
        }

        return static::parseForDocument($tokenizer, $parseContext, $unknownTagHandler);
    }

    protected static function parseForDocument(Tokenizer $tokenizer, ParseContext $parseContext, Closure $unknownTagHandler = null): BlockBody
    {
        $blank = true;
        $nodeList = [];

        while (($token = $tokenizer->shift()) !== null) {
            if ($token === '') {
                continue;
            }

            if (str_starts_with($token, self::TAGSTART)) {
                $nodeList = self::whitespaceHandler($token, $parseContext, $nodeList);

                if (preg_match(self::FULL_TOKEN, $token, $matches) !== 1) {
                    static::handleInvalidTagToken($token, $parseContext);
                }

                $tagName = $matches[2];
                $markup = $matches[4];

                if ($parseContext->lineNumber !== null) {
                    $parseContext->lineNumber += substr_count($matches[1], PHP_EOL) + substr_count($matches[3], PHP_EOL);
                }

                if ($tagName === 'liquid') {
                    dd('parseLiquidTag', $token, $markup);

                    continue;
                }

                /** @var class-string<Tag>|null $tagClass */
                $tagClass = Template::registeredTags()[$tagName] ?? null;

                if ($tagClass === null) {
                    if ($unknownTagHandler !== null) {
                        $unknownTagHandler($tagName, $markup);
                    } else {
                        throw SyntaxException::unknownTag($tagName, $markup, $parseContext);
                    }

                    return new BlockBody($nodeList, $blank);
                }

                $tag = $tagClass::parse($tagName, $markup, $tokenizer, $parseContext);
                $blank = $blank && $tag->blank();
                $nodeList[] = $tag;
            } elseif (str_starts_with($token, self::VARSTART)) {
                $nodeList = static::whitespaceHandler($token, $parseContext, $nodeList);
                $nodeList[] = static::createVariable($token, $parseContext);
                $blank = false;
            } else {
                if ($parseContext->trimWhitespace) {
                    $token = ltrim($token);
                }
                $parseContext->trimWhitespace = false;
                $nodeList[] = $token;
                $blank = $blank && preg_match(self::WHITESPACE_OR_NOTHING, $token) === 1;
            }

            $parseContext->lineNumber = $tokenizer->getLineNumber();
        }

        return new BlockBody($nodeList, $blank);
    }

    /**
     * @return never-return
     */
    protected static function handleInvalidTagToken(string $token, ParseContext $parseContext): void
    {
        if (str_ends_with($token, '%}')) {
            dd('yield', $token);
        }

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
}
