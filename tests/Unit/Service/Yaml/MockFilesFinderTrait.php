<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

trait MockFilesFinderTrait
{
    /**
     * @param array<string,array<string,array<int,string>>> $filesMap
     *
     * @return MockObject&FilesFinder
     */
    private function mockFilesFinder(array $filesMap, TestCase $testCase): MockObject
    {
        $fileMapFilter = $testCase->getMockBuilder(FilesFinder::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $fileMapFilter->method('getFilesMap')->willReturn($filesMap);

        return $fileMapFilter;
    }
}
