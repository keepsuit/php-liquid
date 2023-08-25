<?php

namespace Keepsuit\Liquid\Parse;

enum ErrorMode
{
    /**
     * acts like liquid 2.5 and silently ignores malformed tags in most cases.
     */
    case Lax;

    /**
     * is the default and will give deprecation warnings when invalid syntax is used.
     */
    case Warn;

    /**
     * will enforce correct syntax.
     */
    case Strict;
}
