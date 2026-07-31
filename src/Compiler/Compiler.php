<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Template;

class Compiler
{
    public function compile(Template $template): string
    {
        $context = new CompilerContext;
        $root = $context->exportSerializedValue($template->root);

        if ($root === null) {
            throw new \RuntimeException('Unable to safely reconstruct the template root for compilation.');
        }

        $renderBody = $context->compileNode($template->root);

        if ($renderBody === null) {
            return $this->compileInterpreterFallback($root);
        }

        $builder = new CodeBuilder;

        $builder->writeLine('<?php');
        $builder->writeLine('');
        $builder->writeLine('return new \\Keepsuit\\Liquid\\Compiler\\CompiledTemplate(');
        $builder->indent();
        $builder->writeLine($root.',');
        $builder->writeLine('null,');
        $builder->writeLine('static function (\\Keepsuit\\Liquid\\Render\\RenderContext $context): string {');
        $builder->indent();
        $builder->writeLine('return '.$renderBody.';');
        $builder->dedent();
        $builder->writeLine('},');
        $builder->dedent();
        $builder->writeLine(');');

        return $builder->getSource();
    }

    protected function compileInterpreterFallback(string $root): string
    {
        $builder = new CodeBuilder;

        $builder->writeLine('<?php');
        $builder->writeLine('');
        $builder->writeLine('return new \\Keepsuit\\Liquid\\Compiler\\CompiledTemplate(');
        $builder->indent();
        $builder->writeLine($root);
        $builder->dedent();
        $builder->writeLine(');');

        return $builder->getSource();
    }
}
