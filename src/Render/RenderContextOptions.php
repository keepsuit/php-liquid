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
        /**
         * Allow parsing the template when it is rendered if it has not been parsed yet.
         */
        public readonly bool $lazyParsing = true,
    ) {}

    public function cloneWith(
        ?bool $strictVariables = null,
        ?bool $strictFilters = null,
        ?bool $rethrowErrors = null,
        ?bool $lazyParsing = null,
    ): RenderContextOptions {
        return new RenderContextOptions(
            strictVariables: $strictVariables ?? $this->strictVariables,
            strictFilters: $strictFilters ?? $this->strictFilters,
            rethrowErrors: $rethrowErrors ?? $this->rethrowErrors,
            lazyParsing: $lazyParsing ?? $this->lazyParsing,
        );
    }
}
