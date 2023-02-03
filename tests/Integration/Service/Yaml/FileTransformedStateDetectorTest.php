<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Integration\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileTransformedStateDetector;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class FileTransformedStateDetectorTest extends KernelTestCase
{
    private FileTransformedStateDetector $stateDetector;

    /**
     * @dataProvider getDataForTest
     */
    public function testIsTransformed(bool $expected, string $filePath): void
    {
        self::assertSame($expected, $this->stateDetector->isTransformed($filePath));
    }

    /**
     * @return iterable<array{ 0: bool, 1: string }>
     */
    public function getDataForTest(): iterable
    {
        yield [true, __DIR__.'/../../../../examples/outgoing.en.yaml'];
        yield [false, __DIR__.'/../../../../examples/income.en.yaml'];
    }

    protected function setUp(): void
    {
        $this->stateDetector = self::getContainer()->get(FileTransformedStateDetector::class);
    }
}
