<?php

namespace Keepsuit\Liquid;

class TemplateSharedState
{
    /**
     * @var array<\Throwable>
     */
    public array $errors = [];

    /**
     * @var array<string,Template>
     */
    public array $partialsCache = [];

    /**
     * @var array<string,mixed>
     */
    public array $outputs = [];
}
