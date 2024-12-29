<?php

namespace Keepsuit\Liquid\Profiler;

enum ProfileType: string
{
    case Template = 'template';
    case Tag = 'tag';
    case Variable = 'variable';
}
