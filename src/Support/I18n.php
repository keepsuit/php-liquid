<?php

namespace Keepsuit\Liquid\Support;

use Keepsuit\Liquid\Exceptions\TranslationException;

class I18n
{
    public readonly string $path;

    protected ?array $locale = null;

    public function __construct(string $path = null)
    {
        $this->path = $path ?? __DIR__.'/../../locales/en.yml';
    }

    public function translate(string $key, array $vars = []): string
    {
        return $this->interpolate($this->deepFetchTranslation($key), $vars);
    }

    protected function deepFetchTranslation(string $key): string
    {
        $result = array_reduce(
            explode('.', $key),
            function (mixed $translation, string $current) use ($key) {
                if (! is_array($translation)) {
                    throw TranslationException::invalidTranslation($key, $this->path);
                }

                return $translation[$current] ?? throw TranslationException::keyNotExists($key, $this->path);
            },
            $this->getLocale()
        );

        if (! (is_string($result) || is_numeric($result))) {
            throw TranslationException::invalidTranslation($key, $this->path);
        }

        return (string) $result;
    }

    protected function interpolate(string $translation, array $vars = []): string
    {
        $result = preg_replace_callback(
            '/%\{(\w+)}/',
            fn (array $matches) => (string) ($vars[$matches[1]] ?? $matches[0]),
            $translation
        );

        if ($result === null) {
            throw TranslationException::interpolationFailed($translation, $vars);
        }

        return $result;
    }

    protected function getLocale(): array
    {
        if ($this->locale === null) {
            $this->locale = YamlParser::parseFile($this->path);
        }

        return $this->locale;
    }
}
