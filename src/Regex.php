<?php

namespace Keepsuit\Liquid;

class Regex
{
    const FilterSeparator = '\|';

    const ArgumentSeparator = ',';

    const FilterArgumentSeparator = ':';

    const VariableAttributeSeparator = '.';

    const WhitespaceControl = '-';

    const TagStart = '\{%';

    const TagEnd = '%\}';

    const TagName = '#|\w+';

    const VariableSignature = '\(?[\w\-\.\[\]]\)?';

    const VariableSegment = '[\w\-]';

    const VariableStart = '\{\{';

    const VariableEnd = '\}\}';

    const VariableIncompleteEnd = '\}\}?';

    const QuotedString = '"[^"]*"|\'[^\']*\'';

    const QuotedFragment = self::QuotedString.'|(?:[^\s,\|\'"]|'.self::QuotedString.')+';

    const TagAttributes = '(\w[\w-]*)\s*\:\s*('.self::QuotedFragment.')';

    const AnyStartingTag = self::TagStart.'|'.self::VariableStart;

    const PartialTemplateParser = self::TagStart.'.*?'.self::TagEnd.'|'.self::VariableStart.'.*?'.self::VariableIncompleteEnd;

    const TemplateParser = '('.self::PartialTemplateParser.'|'.self::AnyStartingTag.')';

    const VariableParser = '\[(?>[^\[\]]+|\g<0>)*\]|'.self::VariableSegment.'+\??';
}
