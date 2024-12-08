<?php

declare(strict_types=1);

/*
 * This file is part of the TransMaintain.
 *
 * (c) Anatoliy Melnikov <5785276@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Aeliot\Bundle\TransMaintain\Test\Integration\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileTransformedStateDetector;
use Aeliot\Bundle\TransMaintain\Test\Integration\IntegrationTestCase;

final class FileTransformedStateDetectorTest extends IntegrationTestCase
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
