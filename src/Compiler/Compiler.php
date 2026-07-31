<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Template;

class Compiler
{
    public function compile(Template $template): string
    {
        $builder = new CodeBuilder;
        $serializedRoot = base64_encode(serialize($template->root));

        $builder->writeLine('<?php');
        $builder->writeLine('');
        $builder->writeLine('return new \\Keepsuit\\Liquid\\Compiler\\CompiledTemplate(');
        $builder->indent();
        $builder->writeLine(sprintf(
            "unserialize(base64_decode(%s), ['allowed_classes' => true])",
            var_export($serializedRoot, true),
        ));
        $builder->dedent();
        $builder->writeLine(');');

        return $builder->getSource();
    }
}
