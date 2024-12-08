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

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser;
use PHPUnit\Framework\TestCase;

trait MockFileToSingleLevelArrayParserTrait
{
    /**
     * @param array<string,mixed> $fileTranslations
     */
    private function mockFileToSingleLevelArrayParser(array $fileTranslations, TestCase $testCase): FileToSingleLevelArrayParser
    {
        $fileManipulator = $testCase->getMockBuilder(FileToSingleLevelArrayParser::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $fileManipulator->method('parse')->willReturnCallback(function (string $path) use ($fileTranslations) {
            return $fileTranslations[$path];
        });

        $fileManipulator->method('parseFiles')->willReturnCallback(function (array $files) use ($fileTranslations) {
            $yaml = array_merge(...array_values(array_intersect_key($fileTranslations, array_flip($files))));
            ksort($yaml);

            return $yaml;
        });

        return $fileManipulator;
    }
}
