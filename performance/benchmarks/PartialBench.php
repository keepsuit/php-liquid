<?php

namespace Keepsuit\Liquid\Performance\benchmarks;

use Keepsuit\Liquid\Environment;
use Keepsuit\Liquid\EnvironmentFactory;
use Keepsuit\Liquid\Template;
use Keepsuit\Liquid\Tests\Stubs\StubFileSystem;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\OutputMode;
use PhpBench\Attributes\OutputTimeUnit;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;

#[Iterations(10)]
#[Revs(1)]
#[Warmup(1)]
#[OutputMode('throughput')]
#[OutputTimeUnit('seconds', precision: 3)]
class PartialBench
{
    private const ROOT = '{% render "card" for cards as card %}';

    private const CARD = '{{ card.title }}:{{ card.position }};';

    private Environment $environment;

    private Template $template;

    #[BeforeMethods('setUpCold')]
    public function benchColdRender(): void
    {
        $output = $this->environment
            ->parseString(self::ROOT)
            ->render($this->context());

        $this->assertOutput($output);
    }

    #[BeforeMethods('setUpCold')]
    public function benchColdStream(): void
    {
        $this->consume($this->environment->parseString(self::ROOT)->stream($this->context()));
    }

    #[BeforeMethods('setUpCached')]
    public function benchCachedRender(): void
    {
        $this->assertOutput($this->template->render($this->context()));
    }

    #[BeforeMethods('setUpCached')]
    public function benchCachedStream(): void
    {
        $this->consume($this->template->stream($this->context()));
    }

    public function setUpCold(): void
    {
        $this->environment = $this->newEnvironment();
    }

    public function setUpCached(): void
    {
        $this->environment = $this->newEnvironment();
        $this->template = $this->environment->parseString(self::ROOT);
    }

    private function newEnvironment(): Environment
    {
        return EnvironmentFactory::new()
            ->setFilesystem(new StubFileSystem(['card' => self::CARD]))
            ->build();
    }

    private function context(): \Keepsuit\Liquid\Render\RenderContext
    {
        return $this->environment->newRenderContext(staticData: [
            'cards' => array_map(
                static fn (int $position): array => [
                    'title' => 'Card',
                    'position' => $position,
                ],
                range(1, 32),
            ),
        ]);
    }

    private function consume(\Generator $stream): void
    {
        $length = 0;

        foreach ($stream as $chunk) {
            if (! is_string($chunk)) {
                throw new \RuntimeException('Partial stream yielded a non-string value');
            }

            $length += strlen($chunk);
        }

        if ($length === 0) {
            throw new \RuntimeException('Partial stream produced no output');
        }
    }

    private function assertOutput(string $output): void
    {
        if ($output === '') {
            throw new \RuntimeException('Partial render produced no output');
        }
    }
}
