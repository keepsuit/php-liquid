<?php

namespace Keepsuit\Liquid;

class TranslationException extends \Exception
{
    public static function keyNotExists(string $key, string $path): TranslationException
    {
        return new TranslationException(sprintf("Translation for '%s' does not exist in locale: '%s'", $key, $path));
    }

    public static function invalidTranslation(string $key, string $path): TranslationException
    {
        return new TranslationException(sprintf("Translation for '%s' is invalid in locale: '%s'", $key, $path));
    }

    public static function interpolationFailed(string $translation, array $vars): TranslationException
    {
        return new TranslationException(sprintf("Interpolation failed for translation: '%s' with vars: %s", $translation, json_encode($vars)));
    }
}
