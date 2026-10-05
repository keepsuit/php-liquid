<?php

namespace Keepsuit\Liquid\Compiler;

use Keepsuit\Liquid\Condition\Condition;
use Keepsuit\Liquid\Contracts\CanBeCompiled;
use Keepsuit\Liquid\Contracts\CanBeExported;
use Keepsuit\Liquid\Contracts\CanBeStreamed;
use Keepsuit\Liquid\Contracts\Disableable;
use Keepsuit\Liquid\Nodes\BodyNode;
use Keepsuit\Liquid\Nodes\Document;
use Keepsuit\Liquid\Nodes\Node;
use Keepsuit\Liquid\Nodes\Raw;
use Keepsuit\Liquid\Nodes\Text;
use Keepsuit\Liquid\Nodes\Variable;
use Keepsuit\Liquid\Nodes\VariableLookup;
use Keepsuit\Liquid\Render\RenderContext;
use Keepsuit\Liquid\Tag;
use Keepsuit\Liquid\Tags\AssignTag;
use Keepsuit\Liquid\Tags\BreakTag;
use Keepsuit\Liquid\Tags\CaptureTag;
use Keepsuit\Liquid\Tags\CaseTag;
use Keepsuit\Liquid\Tags\ContinueTag;
use Keepsuit\Liquid\Tags\Custom\DynamicRenderTag;
use Keepsuit\Liquid\Tags\CycleTag;
use Keepsuit\Liquid\Tags\DecrementTag;
use Keepsuit\Liquid\Tags\DocTag;
use Keepsuit\Liquid\Tags\EchoTag;
use Keepsuit\Liquid\Tags\ForTag;
use Keepsuit\Liquid\Tags\IfChanged;
use Keepsuit\Liquid\Tags\IfTag;
use Keepsuit\Liquid\Tags\IncrementTag;
use Keepsuit\Liquid\Tags\LiquidTag;
use Keepsuit\Liquid\Tags\RawTag;
use Keepsuit\Liquid\Tags\RenderTag;
use Keepsuit\Liquid\Tags\TableRowTag;
use Keepsuit\Liquid\Tags\UnlessTag;
use Keepsuit\Liquid\TemplateSharedState;

final class CompilerContext
{
    private const INLINE_NODE_CLASSES = [Text::class, Raw::class, BodyNode::class, Document::class];

    private const RUNTIME_OVERRIDE_NODE_CLASSES = [
        AssignTag::class, CaptureTag::class, ForTag::class, LiquidTag::class, RenderTag::class, DynamicRenderTag::class,
        Variable::class, BodyNode::class, Document::class,
    ];

    private const NATIVE_COMPILABLE_NODE_CLASSES = [
        Variable::class, IfTag::class, UnlessTag::class, CaseTag::class,
        ForTag::class, RenderTag::class, DynamicRenderTag::class, AssignTag::class, CaptureTag::class, LiquidTag::class,
    ];

    private const NATIVE_TAG_CLASSES = [
        EchoTag::class, IncrementTag::class, DecrementTag::class, CycleTag::class, BreakTag::class, ContinueTag::class,
    ];

    /** @var array<string, string> */
    private array $classNames = [
        CompiledTemplate::class => 'CompiledTemplate',
        RenderContext::class => 'RenderContext',
        TemplateSharedState::class => 'TemplateSharedState',
    ];

    /**
     * @var array<string,mixed>
     */
    private array $fallbackValues = [];

    /** @var array<int, string> */
    private array $fallbackObjectProperties = [];

    /** @var array<string, string> */
    private array $fallbackLookupProperties = [];

    /** @var array<string, true> */
    private array $arrayReferences = [];

    /** @var array<int, array{node: Node, source: ?string}> */
    private array $extensionSources = [];

    /** @var array<int, array{node: Node, method: string}> */
    private array $renderedBodies = [];

    /** @var array<string, ?string> */
    private array $renderedBodySources = [];

    /** @var array<string, string> */
    private array $renderedBodyMethods = [];

    private int $renderedBodyCount = 0;

    private bool $rendering = false;

    private bool $buffering = true;

    private int $temporaryCount = 0;

    public function temporaryVariable(): string
    {
        return '$temp'.$this->temporaryCount++;
    }

    public function __construct(private CodeBuilder $builder = new CodeBuilder) {}

    public function isRendering(): bool
    {
        return $this->rendering;
    }

    /**
     * Generate the string path while sharing reconstructed values with stream().
     */
    public function compileRender(Document $root): string
    {
        $outerBuilder = $this->builder;
        $this->builder = new CodeBuilder;
        $this->rendering = true;

        try {
            $this->subcompile($root);

            return $this->getSource();
        } finally {
            $this->builder = $outerBuilder;
            $this->rendering = false;
        }
    }

    /**
     * Compile a body inline while preserving render-score accounting.
     */
    public function compileBody(Node $body): static
    {
        $renderScore = $body instanceof BodyNode ? count($body->children()) : 1;
        $source = $this->compileBodySource($body);

        if (! $this->inheritsNativeCompileMethod($body)) {
            $this->write(sprintf(
                '$context->resourceLimits->incrementRenderScore(%s);',
                $this->writeValue($renderScore),
            ));
        }
        $this->writeBodySource($source);

        return $this;
    }

    public function writeCaptureBody(BodyNode $body, string $result): void
    {
        $bufferState = $this->builder->streamBufferState();
        $this->write($result.' = $context->resourceLimits->withCapture(function () use ($context): string {')->indent();
        $this->writeRenderedBody($body, '$output');
        $this->write('return $output;');
        $this->outdent()->write('});');
        $this->builder->setStreamBufferState($bufferState);
    }

    public function writeRenderedBody(BodyNode $body, string $result): void
    {
        $this->writeRenderedBlock($body, $result, function (CompilerContext $context) use ($body): void {
            $context->compileBody($body);
        });
    }

    /** @param \Closure(CompilerContext): void $compile */
    public function writeRenderedBlock(Node $node, string $result, \Closure $compile): void
    {
        $id = spl_object_id($node);
        if (isset($this->renderedBodies[$id])) {
            $this->write($result.' = $this->'.$this->renderedBodies[$id]['method'].'($context);');

            return;
        }

        $method = 'renderBody'.$this->renderedBodyCount++;
        $this->renderedBodies[$id] = ['node' => $node, 'method' => $method];
        $this->renderedBodySources[$method] = null;
        $outerBuilder = $this->builder;
        $rendering = $this->rendering;
        $temporaryCount = $this->temporaryCount;
        $this->builder = new CodeBuilder;
        $this->rendering = true;
        $this->temporaryCount = 0;

        try {
            $this->write('private function '.$method.'(RenderContext $context): string');
            $this->write('{')->indent()->write('$output = "";');
            $this->write('// Shared rendered body.');
            $compile($this);
            $this->write('return $output;')->outdent()->write('}');
            $source = $this->getSource();
        } finally {
            $this->builder = $outerBuilder;
            $this->rendering = $rendering;
            $this->temporaryCount = $temporaryCount;
        }

        // Method names differ, but equal bodies with the same error metadata
        // and reconstructed values can share one implementation.
        $hash = hash('sha256', substr($source, strlen('private function '.$method)));
        if (isset($this->renderedBodyMethods[$hash])) {
            unset($this->renderedBodySources[$method]);
            $method = $this->renderedBodyMethods[$hash];
            $this->renderedBodies[$id]['method'] = $method;
        } else {
            $this->renderedBodyMethods[$hash] = $method;
            $this->renderedBodySources[$method] = $source;
        }
        $this->write($result.' = $this->'.$method.'($context);');
    }

    /** @return array<string, string> */
    public function getRenderedBodySources(): array
    {
        return array_filter($this->renderedBodySources, static fn (?string $source): bool => $source !== null);
    }

    /**
     * @return array{source:string,hasYield:bool,bufferState:array{checked:bool,empty:bool,maxLength:?int}}
     */
    private function compileBodySource(Node $body, bool $emptyBuffer = false): array
    {
        $outerBuilder = $this->builder;
        $this->builder = new CodeBuilder;
        if ($emptyBuffer) {
            $this->builder->setStreamBufferState(['checked' => true, 'empty' => true, 'maxLength' => 0]);
        }

        try {
            $this->subcompile($body);

            return [
                'source' => $this->builder->getSource(),
                'hasYield' => $this->builder->yieldCount() > 0
                    || (! $this->rendering && $this->buffering && ! $this->builder->streamBufferState()['empty']),
                'bufferState' => $this->builder->streamBufferState(),
            ];
        } finally {
            $this->builder = $outerBuilder;
        }
    }

    /** @param array{source:string,hasYield:bool,bufferState:array{checked:bool,empty:bool,maxLength:?int}} $source */
    private function writeBodySource(array $source): void
    {
        $this->writeSource($source['source']);
        if ($source['hasYield']) {
            $this->builder->markYield();
        }
        $this->builder->setStreamBufferState($source['bufferState']);
    }

    private function writeSource(string $source): void
    {
        $this->builder->writeLines($source, skipEmptyLines: true);
    }

    public function compileRootBody(BodyNode $body): void
    {
        $renderScore = count($body->children());
        $source = $this->compileBodySource($body, emptyBuffer: true);

        if ($this->rendering) {
            $this->write('$output = "";');
        } elseif ($this->buffering && $source['hasYield']) {
            $this->resetStreamBuffer();
        }

        if (! $this->inheritsNativeCompileMethod($body)) {
            $this->write(sprintf(
                '$context->resourceLimits->incrementRenderScore(%s);',
                $this->writeValue($renderScore),
            ));
        }
        $this->writeBodySource($source);

        if ($this->rendering) {
            $this->write('return $output;');
        } elseif ($this->buffering && $source['hasYield']) {
            $this->flushStreamBuffer(reset: false);
        } elseif (! $source['hasYield']) {
            $this->write('return [];');
        }
    }

    public function write(string $line = ''): static
    {
        $this->builder->writeLine($line);

        return $this;
    }

    public function writeYield(string $expression, string $suffix = ';'): static
    {
        $this->builder->markYield();

        return $this->write('yield '.$expression.$suffix);
    }

    private function resetStreamBuffer(): static
    {
        $this->write('$buffer = "";');
        $this->builder->setStreamBufferState(['checked' => true, 'empty' => true, 'maxLength' => 0]);

        return $this;
    }

    /**
     * Write a trusted compiler or plugin fragment without data encoding.
     */
    public function raw(string $fragment): static
    {
        $this->builder->writeRaw($fragment);

        return $this;
    }

    public function indent(): static
    {
        $this->builder->indent();

        return $this;
    }

    public function outdent(): static
    {
        $this->builder->dedent();

        return $this;
    }

    public function canBufferConstantOutput(int $length): bool
    {
        return $this->buffering && $this->builder->streamBufferState()['empty'] && $length < 4096;
    }

    public function writeOutput(string $expression, string $suffix = ';', ?int $length = null): static
    {
        $maxLength = $length !== null && ! $this->rendering && $this->buffering
            ? $this->builder->streamBufferState()['maxLength']
            : null;
        if ($this->rendering) {
            $this->write('$output .= '.$expression.$suffix);
        } elseif ($this->buffering) {
            $this->write('$buffer .= '.$expression.$suffix);
            $this->builder->setStreamBufferState(['checked' => false, 'empty' => false, 'maxLength' => null]);
        } else {
            $this->writeYield($expression, $suffix);
        }
        if ($suffix === ';') {
            if (! $this->rendering && $this->buffering && $length !== null && $maxLength !== null) {
                $maxLength += $length;
                $this->builder->setStreamBufferState([
                    'checked' => $maxLength < 4096,
                    'empty' => $maxLength === 0,
                    'maxLength' => $maxLength,
                ]);
            }
            $this->flushStreamBufferIfFull();
        }

        return $this;
    }

    public function flushStreamBufferIfFull(): void
    {
        if (! $this->rendering && $this->buffering && ! $this->builder->streamBufferState()['checked']) {
            $this->write('if (strlen($buffer) >= 4096) {')->indent();
            $this->writeYield('$buffer')->resetStreamBuffer();
            $this->outdent()->write('}');
            $this->builder->setStreamBufferState(['checked' => true, 'empty' => false, 'maxLength' => 4095]);
        }
    }

    public function flushStreamBuffer(bool $reset = true): void
    {
        if (! $this->rendering && $this->buffering && ! $this->builder->streamBufferState()['empty']) {
            $this->write('if ($buffer !== "") {')->indent();
            $this->writeYield('$buffer');
            if ($reset) {
                $this->resetStreamBuffer();
            }
            $this->outdent()->write('}');
            $this->builder->setStreamBufferState(['checked' => $reset, 'empty' => $reset, 'maxLength' => $reset ? 0 : null]);
        }
    }

    public function writeText(string $value): static
    {
        if ($value !== '') {
            $this->writeOutput($this->writeLiteral($value), length: strlen($value));
        }

        return $this;
    }

    public function writeLineComment(?int $lineNumber): static
    {
        if ($lineNumber !== null) {
            $this->write('// line '.$lineNumber);
        }

        return $this;
    }

    public function canInterrupt(Node $node): bool
    {
        return ! $node instanceof Text;
    }

    public function subcompile(Node $node): static
    {
        if ($node instanceof Text || $node instanceof Raw || $node instanceof BodyNode || $node instanceof Document) {
            $custom = ! in_array($node::class, self::INLINE_NODE_CLASSES, true);
            if ($custom && $this->inheritsNativeCompileMethod($node)) {
                if ($node instanceof Document) {
                    $value = $this->writeRuntimeValue($node);
                    if ($this->rendering) {
                        $this->write('return '.$value.'->render($context);');
                    } else {
                        $this->writeYield('from '.$value.'->stream($context)');
                    }
                } else {
                    $this->compileFallback($node);
                }

                return $this;
            }
            if ($custom) {
                $this->builder->setStreamBufferState(['checked' => false, 'empty' => false, 'maxLength' => null]);
            }
            $node->compile($this);
            if ($custom) {
                $this->builder->setStreamBufferState(['checked' => false, 'empty' => false, 'maxLength' => null]);
            }

            return $this;
        }

        $this->compileNode($node);

        return $this;
    }

    private function compileNode(Node $node): void
    {
        $this->flushStreamBufferIfFull();
        // Native documentation never renders or throws. Keep the preceding
        // stream flush and the body's render score/interrupt checks.
        if ($node::class === DocTag::class) {
            return;
        }

        // These exact tags only append an interrupt to the final render context.
        // The preceding buffer flush keeps its original exception boundary.
        if ($node::class === BreakTag::class || $node::class === ContinueTag::class) {
            $this->writeLineComment($node->lineNumber());
            $node->compileNative($this);

            return;
        }

        // Fixed text cannot throw while appending to the string accumulator.
        // Streams retain the boundary: callers can throw into a yielded chunk.
        if ($this->rendering) {
            if ($node::class === Variable::class && $node->constantOutput() !== null) {
                $this->writeLineComment($node->lineNumber());
                $node->compile($this);

                return;
            }
            if ($node::class === RawTag::class && $node->getBody()::class === Raw::class) {
                $this->writeLineComment($node->lineNumber());
                $this->writeText($node->getBody()->value);

                return;
            }
        }

        $checkpoint = $this->builder->checkpoint();
        $fallbackValueCount = count($this->fallbackValues);
        $extensionSourceCount = count($this->extensionSources);
        $renderedBodyCount = count($this->renderedBodies);
        $renderedBodySourceCount = count($this->renderedBodySources);
        $renderedBodyMethodCount = count($this->renderedBodyMethods);
        $classNameCount = count($this->classNames);
        $rendering = $this->rendering;
        $id = spl_object_id($node);
        $fallback = isset($this->extensionSources[$id]) && $this->extensionSources[$id]['source'] === null;
        $fallback = $this->inheritsNativeCompileMethod($node) || $fallback;

        // Extension fragments may return from their node or yield before failing.
        // Keep their lazy boundary; native nodes can use an inline try/catch.
        $extension = $node instanceof CanBeCompiled && ! in_array($node::class, self::NATIVE_COMPILABLE_NODE_CLASSES, true);
        $lazy = $extension && ! $fallback;

        try {
            $this->writeNodeStart($node, $lazy);
            if ($lazy) {
                $this->writeSource($this->compileExtensionSource($node));
            } else {
                $this->writeLineComment($node->lineNumber());

                if (! $this->compileNativeTag($node)) {
                    if ($node instanceof CanBeCompiled && ! $fallback) {
                        $node->compile($this);
                    } else {
                        $this->compileFallback($node);
                    }
                }
            }
            $this->rendering = $rendering;
            $this->writeNodeEnd($node, $lazy);
        } catch (UnsupportedNodeException) {
            $this->rendering = $rendering;
            $this->rollbackCompilation($checkpoint, $fallbackValueCount);
            $this->extensionSources = array_slice($this->extensionSources, 0, $extensionSourceCount, preserve_keys: true);
            $this->renderedBodies = array_slice($this->renderedBodies, 0, $renderedBodyCount, preserve_keys: true);
            $this->renderedBodySources = array_slice($this->renderedBodySources, 0, $renderedBodySourceCount, preserve_keys: true);
            $this->renderedBodyMethods = array_slice($this->renderedBodyMethods, 0, $renderedBodyMethodCount, preserve_keys: true);
            $this->classNames = array_slice($this->classNames, 0, $classNameCount, preserve_keys: true);
            if ($extension) {
                $this->extensionSources[$id] = ['node' => $node, 'source' => null];
            }
            $this->writeNodeStart($node, false);
            $this->writeLineComment($node->lineNumber());
            $this->compileFallback($node);
            $this->writeNodeEnd($node, false);
        }
    }

    private function inheritsNativeCompileMethod(Node $node): bool
    {
        if (! ($node instanceof AssignTag || $node instanceof CaptureTag || $node instanceof ForTag || $node instanceof LiquidTag || $node instanceof RenderTag
            || $node instanceof Variable || $node instanceof BodyNode || $node instanceof Document)
            || in_array($node::class, self::RUNTIME_OVERRIDE_NODE_CLASSES, true)) {
            return false;
        }

        $compilerClass = (new \ReflectionMethod($node, 'compile'))->getDeclaringClass()->getName();

        return $node::class !== $compilerClass && in_array($compilerClass, self::RUNTIME_OVERRIDE_NODE_CLASSES, true);
    }

    private function compileNativeTag(Node $node): bool
    {
        if ($node::class === IfChanged::class || $node::class === TableRowTag::class) {
            $node->compileNative($this);

            return true;
        }

        if ($node::class === RawTag::class && $node->getBody()::class === Raw::class) {
            $this->writeText($node->getBody()->value);

            return true;
        }

        // Avoid adding CanBeCompiled to these tags: streamed subclasses must
        // retain their existing unbuffered extension boundary in BodyNode.
        if (in_array($node::class, self::NATIVE_TAG_CLASSES, true)) {
            /** @var EchoTag|IncrementTag|DecrementTag|CycleTag|BreakTag|ContinueTag $node */
            $node->compileNative($this);

            return true;
        }

        return false;
    }

    private function compileExtensionSource(Node $node): string
    {
        $id = spl_object_id($node);
        if (isset($this->extensionSources[$id]['source'])) {
            return $this->extensionSources[$id]['source'];
        }

        $outerBuilder = $this->builder;
        $rendering = $this->rendering;
        $buffering = $this->buffering;
        $this->builder = new CodeBuilder;
        $this->rendering = false;
        $this->buffering = false;

        try {
            assert($node instanceof CanBeCompiled);
            $this->writeLineComment($node->lineNumber());
            $node->compile($this);

            if ($this->builder->yieldCount() === 0) {
                $this->write('return [];');
            }

            $source = $this->getSource();
            $this->extensionSources[$id] = ['node' => $node, 'source' => $source];

            return $source;
        } finally {
            $this->builder = $outerBuilder;
            $this->rendering = $rendering;
            $this->buffering = $buffering;
        }
    }

    private function writeNodeStart(Node $node, bool $lazy): void
    {
        if ($lazy) {
            $this->flushStreamBuffer();
            if (! $this->rendering) {
                $this->builder->markYield();
            }
            $this->write(sprintf(
                ($this->rendering ? 'foreach (' : 'yield from ').'$this->yieldNode($context, %s, function () use ($context): iterable {',
                $this->writeValue($node->lineNumber()),
            ));
        } else {
            $this->write('try {');
        }

        $this->indent();
    }

    private function writeNodeEnd(Node $node, bool $lazy): void
    {
        if ($lazy) {
            if ($this->rendering) {
                $this->outdent()->write('}) as $chunk) {');
                $this->indent()->write('$output .= $chunk;');
                $this->outdent()->write('}');
            } else {
                $this->outdent()->write('});');
            }

            return;
        }

        $line = $this->writeValue($node->lineNumber());
        $successBufferState = $this->builder->streamBufferState();
        $this->outdent()->write('} catch (\\Throwable $exception) {');
        // A throw into yield can precede the buffer reset. Catch paths cannot
        // reuse facts about the successful path through this node.
        $this->builder->setStreamBufferState(['checked' => false, 'empty' => false, 'maxLength' => null]);
        $this->indent();
        $this->flushStreamBuffer();
        $error = '$this->compiledErrorOutput($exception, $context->handleError($exception, '.$line.'))';
        if ($this->rendering || $this->buffering) {
            $this->writeOutput('('.$error.' ?? "")');
        } else {
            $value = $this->temporaryVariable();
            $this->write($value.' = '.$error.';');
            $this->write('if ('.$value.' !== null) {')->indent();
            $this->writeOutput($value);
            $this->outdent()->write('}');
        }
        $this->outdent()->write('}');
        $this->builder->setStreamBufferState([
            'checked' => $successBufferState['checked'],
            'empty' => false,
            'maxLength' => $successBufferState['checked'] ? 4095 : null,
        ]);
    }

    /**
     * Compile the node into a lazy generator so the base template can own the
     * runtime error boundary while the surrounding body remains resumable.
     */
    public function compileFallback(Node $node): void
    {
        $value = $this->writeRuntimeValue($node);

        if ($node instanceof Disableable && $node instanceof Tag) {
            $this->write($value.'->ensureTagIsEnabled($context);');
        }

        if (! $this->rendering && $node instanceof CanBeStreamed) {
            $this->flushStreamBuffer();
            $this->writeYield('from '.$value.'->stream($context)');
        } else {
            $this->writeOutput($value.'->render($context)');
        }
    }

    public function writeValue(mixed $value): string
    {
        if ($value instanceof CanBeExported && ($exported = $value->export($this)) !== null) {
            return $exported;
        }

        if ($value instanceof \UnitEnum) {
            return var_export($value, true);
        }

        if (is_string($value)) {
            return $this->writeExpressionString($value);
        }

        if (is_array($value)) {
            return $this->writeArrayValue($value, cached: false);
        }

        if (is_object($value)) {
            return $this->writeSerializedObject($value);
        }

        if (is_resource($value)) {
            throw new \RuntimeException('Unable to safely encode a compiler value containing a resource.');
        }

        return var_export($value, true);
    }

    public function writeClassName(string $class): string
    {
        if (isset($this->classNames[$class])) {
            return $this->classNames[$class];
        }

        $class = ltrim($class, '\\');
        if (isset($this->classNames[$class])) {
            return $this->classNames[$class];
        }

        $namespace = strrpos($class, '\\');
        $name = $namespace === false ? $class : substr($class, $namespace + 1);
        foreach ($this->classNames as $importedName) {
            if (strcasecmp($name, $importedName) === 0) {
                return '\\'.$class;
            }
        }

        return $this->classNames[$class] = $name;
    }

    /** @return list<string> */
    public function getImportedClasses(): array
    {
        return array_keys($this->classNames);
    }

    /**
     * Describe native lookups as immutable arrays instead of reconstructed objects.
     * Complex and custom expressions retain the existing evaluator path.
     */
    public function writeLookupValue(mixed $value): ?string
    {
        if (is_scalar($value) || $value === null) {
            return $this->writeValue($value);
        }
        if (! $value instanceof VariableLookup || $value::class !== VariableLookup::class) {
            return null;
        }
        foreach ($value->lookups as $lookup) {
            if (! is_string($lookup) && ! is_int($lookup)) {
                return null;
            }
        }

        return $this->writeValue([$value->name, $value->lookups]);
    }

    public function writeVariableExpression(mixed $value): string
    {
        // Variable's output helpers complete evaluation if the lookup resolves
        // to another CanBeEvaluated value rather than a scalar.
        if ($value instanceof VariableLookup && $value::class === VariableLookup::class) {
            if ($value->lookups === []) {
                return $this->writeClassName(VariableLookup::class).'::evaluateName($context, '.$this->writeValue($value->name).')';
            }

            return $this->writeClassName(VariableLookup::class).'::evaluateParts($context, '
                .$this->writeValue($value->name).', '.$this->writeCachedValue($value->lookups).')';
        }

        $source = $this->writeCachedValue($value);

        return is_scalar($value) || $value === null
            ? $source
            : '$context->evaluate('.$source.')';
    }

    /**
     * Resolve an expression fully, including evaluators returned by a lookup.
     */
    public function writeEvaluatedExpression(mixed $value): string
    {
        $source = $this->writeVariableExpression($value);

        return $value instanceof VariableLookup && $value::class === VariableLookup::class
            ? '$context->evaluate('.$source.')'
            : $source;
    }

    public function writeConditionExpression(Condition $condition): string
    {
        return $condition::class === Condition::class
            ? $condition->compileExpression($this)
            : $this->writeRuntimeValue($condition).'->evaluate($context)';
    }

    private function writeSerializedObject(object $value): string
    {
        // Keep generated artifacts independent from Symfony's object exporter.
        $this->assertNoResources($value);

        try {
            $serialized = serialize($value);
        } catch (\Throwable $exception) {
            throw new \RuntimeException('Unable to safely encode a compiler value.', previous: $exception);
        }

        return '\\unserialize('.$this->writeValue($serialized).')';
    }

    /**
     * @param  array<int,true>  $seenObjects
     * @param  array<string,true>  $seenReferences
     */
    private function assertNoResources(mixed $value, array &$seenObjects = [], array &$seenReferences = []): void
    {
        if (is_resource($value)) {
            throw new \RuntimeException('Unable to safely encode a compiler value containing a resource.');
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $reference = \ReflectionReference::fromArrayElement($value, $key);
                if ($reference !== null) {
                    $referenceId = $reference->getId();
                    if (isset($seenReferences[$referenceId])) {
                        continue;
                    }
                    $seenReferences[$referenceId] = true;
                }
                $this->assertNoResources($item, $seenObjects, $seenReferences);
            }

            return;
        }

        if (! is_object($value)) {
            return;
        }

        $objectId = spl_object_id($value);
        if (isset($seenObjects[$objectId])) {
            return;
        }

        $seenObjects[$objectId] = true;
        // Casting includes inherited private state once, omits static and
        // uninitialized properties, and preserves array reference identities.
        $this->assertNoResources((array) $value, $seenObjects, $seenReferences);
    }

    public function writeRuntimeValue(mixed $value): string
    {
        return $this->registerFallbackValue($value);
    }

    /**
     * Keep expression objects out of hot calls while leaving scalar arrays inline.
     */
    public function writeCachedValue(mixed $value): string
    {
        if (is_object($value)) {
            return $this->writeRuntimeValue($value);
        }

        if (! is_array($value)) {
            return $this->writeValue($value);
        }

        return $this->writeArrayValue($value, cached: true);
    }

    private function writeArrayValue(array $value, bool $cached): string
    {
        $entries = [];
        $isList = array_is_list($value);

        foreach ($value as $key => $item) {
            $reference = \ReflectionReference::fromArrayElement($value, $key);
            $referenceId = $reference?->getId();
            if ($referenceId !== null) {
                if (isset($this->arrayReferences[$referenceId])) {
                    throw new \RuntimeException('Unable to safely encode a recursive compiler array.');
                }
                $this->arrayReferences[$referenceId] = true;
            }

            try {
                $entries[] = ($isList ? '' : $this->writeValue($key).' => ').($cached
                    ? $this->writeCachedValue($item)
                    : $this->writeValue($item));
            } finally {
                if ($referenceId !== null) {
                    unset($this->arrayReferences[$referenceId]);
                }
            }
        }

        return '['.implode(', ', $entries).']';
    }

    /**
     * @return array<string,mixed>
     */
    public function getFallbackValues(): array
    {
        return $this->fallbackValues;
    }

    private function registerFallbackValue(mixed $value): string
    {
        if (is_object($value) && isset($this->fallbackObjectProperties[spl_object_id($value)])) {
            return '$this->'.$this->fallbackObjectProperties[spl_object_id($value)];
        }

        $lookupKey = $this->staticLookupKey($value);
        if ($lookupKey !== null && isset($this->fallbackLookupProperties[$lookupKey])) {
            return '$this->'.$this->fallbackLookupProperties[$lookupKey];
        }

        $property = 'value'.count($this->fallbackValues);
        $this->fallbackValues[$property] = $value;

        if (is_object($value)) {
            $this->fallbackObjectProperties[spl_object_id($value)] = $property;
        }

        if ($lookupKey !== null) {
            $this->fallbackLookupProperties[$lookupKey] = $property;
        }

        return '$this->'.$property;
    }

    private function staticLookupKey(mixed $value): ?string
    {
        if (! $value instanceof VariableLookup || $value::class !== VariableLookup::class) {
            return null;
        }

        foreach ($value->lookups as $lookup) {
            if (! is_string($lookup) && ! is_int($lookup)) {
                return null;
            }
        }

        return serialize([$value->name, $value->lookups]);
    }

    private function rollbackFallbackValues(int $count): void
    {
        foreach (array_slice($this->fallbackValues, $count) as $value) {
            if (is_object($value)) {
                unset($this->fallbackObjectProperties[spl_object_id($value)]);
            }
            if (($lookupKey = $this->staticLookupKey($value)) !== null) {
                unset($this->fallbackLookupProperties[$lookupKey]);
            }
        }

        $this->fallbackValues = array_slice($this->fallbackValues, 0, $count, preserve_keys: true);
    }

    /**
     * Restore compiler state after a node's direct or native compiler path fails.
     *
     * @param  array{sourceLength:int,indentLevel:int,yieldCount:int,bufferState:array{checked:bool,empty:bool,maxLength:?int},bufferScopes:list<array{checked:bool,empty:bool,maxLength:?int}>}  $checkpoint
     */
    private function rollbackCompilation(array $checkpoint, int $fallbackValueCount): void
    {
        $this->builder->rollback($checkpoint);
        $this->rollbackFallbackValues($fallbackValueCount);
    }

    public function getSource(): string
    {
        return $this->builder->getSource();
    }

    private function writeLiteral(string $value): string
    {
        $value = strtr($value, [
            '\\' => '\\\\',
            '"' => '\\"',
            '$' => '\\$',
            "\n" => '\\n',
            "\r" => '\\r',
            "\t" => '\\t',
            "\v" => '\\v',
            "\e" => '\\e',
            "\f" => '\\f',
        ]);

        $value = preg_replace_callback(
            '/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F\\x7F]/',
            static fn (array $match): string => sprintf('\\x%02X', ord($match[0])),
            $value,
        );

        assert($value !== null);

        return '"'.$value.'"';
    }

    private function writeExpressionString(string $value): string
    {
        if (preg_match('/[\\x00-\\x1F\\x7F]/', $value) === 1) {
            return $this->writeLiteral($value);
        }

        return "'".str_replace(
            ['\\', "'"],
            ['\\\\', "\\'"],
            $value,
        )."'";
    }
}
