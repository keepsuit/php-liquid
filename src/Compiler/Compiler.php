<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\ParsedTemplate;

class Compiler
{
    /**
     * Write a requireable compiled artifact; the generated source is not validated until it is loaded.
     */
    public function compileToFile(ParsedTemplate $template, string $compiledPath): void
    {
        $directory = dirname($compiledPath);

        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            throw new \RuntimeException(sprintf('Unable to create compiled template directory: %s', $directory));
        }

        $source = $this->compile($template);
        $temporaryPath = $directory.'/.'.basename($compiledPath).'.tmp-'.bin2hex(random_bytes(8));

        try {
            $bytesWritten = file_put_contents($temporaryPath, $source);

            if ($bytesWritten !== strlen($source)) {
                throw new \RuntimeException(sprintf('Unable to write compiled template artifact: %s', $compiledPath));
            }

            $this->publishArtifact($temporaryPath, $compiledPath);

            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($compiledPath, true);
            }
        } finally {
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
        }
    }

    private function publishArtifact(string $temporaryPath, string $compiledPath): void
    {
        set_error_handler(static fn (): bool => true);

        try {
            $published = rename($temporaryPath, $compiledPath);
        } finally {
            restore_error_handler();
        }

        if (! $published) {
            throw new \RuntimeException(sprintf('Unable to publish compiled template artifact: %s', $compiledPath));
        }
    }

    public function compile(ParsedTemplate $template): string
    {
        $bodyContext = new CompilerContext;
        $bodyContext->subcompile($template->root);

        $body = $bodyContext->getSource();
        $renderBody = $bodyContext->compileRender($template->root);
        $name = $bodyContext->writeValue($template->root->name);
        $state = $template->getState();
        $stateArguments = [];
        if ($state->partials !== []) {
            $stateArguments[] = 'partials: '.$bodyContext->writeValue($state->partials);
        }
        if ($state->outputs->all() !== []) {
            try {
                $stateArguments[] = 'outputs: '.$bodyContext->writeValue($state->outputs);
            } catch (\Throwable $exception) {
                throw new \RuntimeException(sprintf(
                    'Unable to serialize the outputs of template %s.',
                    $template->root->name ?? '<unnamed>',
                ), previous: $exception);
            }
        }
        $fallbackValues = $bodyContext->getFallbackValues();
        $renderedBodies = $bodyContext->getRenderedBodySources();
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
            'xxh128',
            $name.$body.$renderBody.implode('', $fallbackValueSource).implode('', $renderedBodies)
                .implode('', $bodyContext->getImportedClasses()),
        ), 0, 32);

        $builder = new CodeBuilder;
        $builder
            ->writeLine('<?php')
            ->writeLine()
            ->writeLine('// Template: '.str_replace('?>', '? >', $template->root->name === null ? '<unnamed>' : $name))
            ->writeLine()
            ->writeLine('namespace Keepsuit\\Liquid\\Compiler\\Generated;')
            ->writeLine();

        foreach ($bodyContext->getImportedClasses() as $class) {
            $builder->writeLine('use '.$class.';');
        }

        $builder->writeLine()
            ->writeLine('if (! class_exists('.$className.'::class, false)) {')
            ->indent()
            ->writeLine('final class '.$className.' extends CompiledTemplate')
            ->writeLine('{')
            ->indent();

        foreach ($fallbackValues as $property => $value) {
            $description = $value instanceof VariableLookup && $value::class === VariableLookup::class
                ? 'Lookup: '.$bodyContext->writeValue($value->name)
                : 'Runtime value: '.$bodyContext->writeValue(get_debug_type($value));
            $builder->writeLine('// '.str_replace('?>', '? >', $description));
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
            ->writeLine('protected function renderCompiled(RenderContext $context): iterable')
            ->writeLine('{')
            ->indent();

        $builder->writeLines($body);

        $builder
            ->dedent()
            ->writeLine('}')
            ->writeLine()
            ->writeLine('protected function renderCompiledString(RenderContext $context): string')
            ->writeLine('{')
            ->indent();

        $builder->writeLines($renderBody);

        $builder->dedent()->writeLine('}');

        foreach ($renderedBodies as $source) {
            $builder->writeLine();
            $builder->writeLines($source);
        }

        $builder
            ->dedent()
            ->writeLine('}')
            ->dedent()
            ->writeLine('}')
            ->writeLine()
            ->writeLine('return new '.$className.($stateArguments === [] ? '' : '(new TemplateSharedState('.implode(', ', $stateArguments).'))').';');

        return $builder->getSource();
    }
}
