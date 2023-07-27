<?php

namespace Keepsuit\Liquid;

use Symfony\Component\Yaml\Yaml;

class YamlParser
{
    public static function parseFile(string $path): array
    {
        if (extension_loaded('yaml')) {
            $content = yaml_parse_file($path);
            if (! is_array($content)) {
                throw new \RuntimeException('Unable to parse yaml file at path: '.$path);
            }

            return $content;
        }

        return (array) Yaml::parseFile($path);
    }
}
