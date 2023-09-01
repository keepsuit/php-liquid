<?php

namespace Keepsuit\Liquid\Parse;

class Regex
{
    const WhitespaceControl = '-';

    const TagStart = '\{%';

    const TagEnd = '%\}';

    const TagName = '#|\w+';

    const VariableSignature = '\(?[\w\-\.\[\]]+\)?';

    const VariableSegment = '[\w\-]';

    const VariableStart = '\{\{';

    const VariableEnd = '\}\}';

    const VariableIncompleteEnd = '\}\}?';

    const QuotedString = '"[^"]*"|\'[^\']*\'';

    const QuotedFragment = self::QuotedString.'|(?:[^\s,\|\'"]|'.self::QuotedString.')+';

    const TagAttributes = '(\w[\w-]*)\s*\:\s*('.self::QuotedFragment.')';

    const AnyStartingTag = self::TagStart.'|'.self::VariableStart;

    const PartialTemplateParser = self::TagStart.'(?:\S|\s)*?'.self::TagEnd.'|'.self::VariableStart.'(?:\S|\s)*?'.self::VariableIncompleteEnd;

    const TemplateParser = '('.self::PartialTemplateParser.'|'.self::AnyStartingTag.')';

    const VariableParser = '\[(?>[^\[\]]+|\g<0>)*\]|'.self::VariableSegment.'+\??';

    const FullTagToken = '/\A'.self::TagStart.self::WhitespaceControl.'?(\s*)('.self::TagName.')(\s*)((\S|\s)*?)'.self::WhitespaceControl.'?'.self::TagEnd.'\z/m';
}
