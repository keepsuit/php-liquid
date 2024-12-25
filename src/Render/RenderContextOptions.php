<?php

namespace Keepsuit\Liquid\Render;

class RenderContextOptions
{
    public function __construct(
        /**
         * Report an error when an undefined variable is used.
         */
        public readonly bool $strictVariables = false,
        /**
         * Report an error when an undefined filter is used.
         */
        public readonly bool $strictFilters = false,
        /**
         * Rethrow exceptions that occur during rendering instead of rendering the error message.
         */
        public readonly bool $rethrowErrors = false,
    ) {}
}
