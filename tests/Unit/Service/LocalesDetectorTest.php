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

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service;

use Aeliot\Bundle\TransMaintain\Service\LocalesDetector;
use Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\MockFilesFinderTrait;
use PHPUnit\Framework\TestCase;

class LocalesDetectorTest extends TestCase
{
    use MockFilesFinderTrait;

    /**
     * @dataProvider getDataForTestGetLocales
     *
     * @param string[] $expected
     * @param array<string,array<string,array<int,string>>> $filesMap
     */
    public function testGetLocales(array $expected, array $filesMap): void
    {
        self::assertSame($expected, $this->createLocalesDetector($filesMap)->getLocales());
    }

    public function getDataForTestGetLocales(): \Generator
    {
        yield [
            ['en'],
            ['a' => ['en' => ['a.en.yaml']]],
        ];
        yield [
            ['en', 'fr'],
            ['a' => ['en' => ['a.en.yaml'], 'fr' => ['a.fr.yaml']]],
        ];
        yield [
            ['de', 'en', 'fr'],
            [
                'a' => ['en' => ['a.en.yaml'], 'fr' => ['a.fr.yaml']],
                'b' => ['de' => ['b.de.yaml']],
            ],
        ];
    }

    /**
     * @param array<string,array<string,array<int,string>>> $filesMap
     */
    private function createLocalesDetector(array $filesMap): LocalesDetector
    {
        return new LocalesDetector($this->mockFilesFinder($filesMap, $this));
    }
}
