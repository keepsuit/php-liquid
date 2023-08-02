<?php

namespace Keepsuit\Liquid;

class BlockBody
{
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

    /**
     * @param  array<BlockBodySection>  $sections
     */
    public static function fromSections(array $sections): BlockBody
    {
        $nodeList = Arr::flatten(array_map(fn (BlockBodySection $section) => $section->nodeList(), $sections), 1);

        return new BlockBody(
            nodeList: $nodeList,
            blank: count($nodeList) === 0
        );
    }
}
