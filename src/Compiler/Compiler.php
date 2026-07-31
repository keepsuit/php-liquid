<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Template;

class Compiler
{
    public function compile(Template $template): string
    {
        $builder = new CodeBuilder;
        $context = new CompilerContext($builder);
        $root = $context->writeValue($template->root);

        $context->write('<?php');
        $context->write();
        $context->write('return new \\Keepsuit\\Liquid\\Compiler\\CompiledTemplate(');
        $context->indent();
        $context->write($root.',');
        $context->write('null,');
        $context->write('static function (\\Keepsuit\\Liquid\\Render\\RenderContext $context): string {');
        $context->indent();
        $context->write('$output = \'\';');
        $context->subcompile($template->root);
        $context->write('return $output;');
        $context->outdent();
        $context->write('},');
        $context->outdent();
        $context->write(');');

        return $context->getSource();
    }
}
