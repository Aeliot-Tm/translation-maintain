<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

trait MockFileToSingleLevelArrayParserTrait
{
    /**
     * @param array<string,mixed> $fileTranslations
     *
     * @return MockObject&FileToSingleLevelArrayParser
     */
    private function mockFileToSingleLevelArrayParser(array $fileTranslations, TestCase $testCase): MockObject
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

        return $fileManipulator;
    }
}
