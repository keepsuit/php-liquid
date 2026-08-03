<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\ParsedTemplate;

class Compiler
{
    public function compile(ParsedTemplate $template): string
    {
        $bodyContext = new CompilerContext;
        $bodyContext->subcompile($template->root);

        $body = $bodyContext->getSource();
        $name = $bodyContext->writeValue($template->root->name);
        $methods = $bodyContext->getMethods();
        $fallbackValues = $bodyContext->getFallbackValues();
        $fallbackValueSource = [];

        foreach ($fallbackValues as $property => $value) {
            try {
                $fallbackValueSource[$property] = $bodyContext->writeValue($value);
            } catch (\Throwable $exception) {
                $nodeDescription = $value instanceof Node
                    ? sprintf(
                        '%s at line %s',
                        $value::class,
                        $value->lineNumber() ?? 'unknown',
                    )
                    : get_debug_type($value);

                throw new \RuntimeException(sprintf(
                    'Unable to safely reconstruct fallback node %s in template %s.',
                    $nodeDescription,
                    $template->root->name ?? '<unnamed>',
                ), previous: $exception);
            }
        }

        $className = 'Template_'.substr(hash(
            'sha256',
            $name.$body.implode('', $methods).implode('', $fallbackValueSource),
        ), 0, 32);

        $builder = new CodeBuilder;
        $builder
            ->writeLine('<?php')
            ->writeLine()
            ->writeLine('namespace Keepsuit\\Liquid\\Compiler\\Generated;')
            ->writeLine()
            ->writeLine('use Keepsuit\\Liquid\\Compiler\\CompiledTemplate;')
            ->writeLine('use Keepsuit\\Liquid\\Render\\RenderContext;')
            ->writeLine('use Keepsuit\\Liquid\\TemplateSharedState;')
            ->writeLine()
            ->writeLine('if (! class_exists('.$className.'::class, false)) {')
            ->indent()
            ->writeLine('final class '.$className.' extends CompiledTemplate')
            ->writeLine('{')
            ->indent();

        foreach ($fallbackValues as $property => $value) {
            $builder->writeLine('private readonly mixed $'.$property.';');
        }

        if ($fallbackValues !== []) {
            $builder
                ->writeLine('public function __construct(TemplateSharedState $state = new TemplateSharedState)')
                ->writeLine('{')
                ->indent();

            foreach ($fallbackValueSource as $property => $source) {
                $builder->writeLine('$this->'.$property.' = '.$source.';');
            }

            $builder
                ->writeLine('parent::__construct($state);')
                ->dedent()
                ->writeLine('}')
                ->writeLine();
        }

        $builder
            ->writeLine('public function name(): ?string')
            ->writeLine('{')
            ->indent()
            ->writeLine('return '.$name.';')
            ->dedent()
            ->writeLine('}')
            ->writeLine()
            ->writeLine('protected function renderCompiled(RenderContext $context): string')
            ->writeLine('{')
            ->indent();

        $builder->writeLine('$output = \'\';');

        foreach (explode("\n", rtrim($body, "\n")) as $line) {
            $builder->writeLine($line);
        }

        $builder
            ->writeLine('return $output;')
            ->dedent()
            ->writeLine('}');

        foreach ($methods as $methodName => $methodSource) {
            $builder
                ->writeLine()
                ->writeLine('private function '.$methodName.'(RenderContext $context): string')
                ->writeLine('{')
                ->indent();

            foreach (explode("\n", rtrim($methodSource, "\n")) as $line) {
                $builder->writeLine($line);
            }

            $builder
                ->dedent()
                ->writeLine('}');
        }

        $builder
            ->dedent()
            ->writeLine('}')
            ->dedent()
            ->writeLine('}')
            ->writeLine()
            ->writeLine('return new '.$className.';');

        return $builder->getSource();
    }
}
