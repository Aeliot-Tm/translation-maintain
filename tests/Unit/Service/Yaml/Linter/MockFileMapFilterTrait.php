<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use PHPUnit\Framework\TestCase;

trait MockFileMapFilterTrait
{
    /**
     * @param array<string,array<string,array<int,string>>> $filesMap
     */
    private function mockFileMapFilter(array $filesMap, TestCase $testCase): FileMapFilter
    {
        $fileMapFilter = $testCase->getMockBuilder(FileMapFilter::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $fileMapFilter->method('getFilesMap')->willReturn($filesMap);

        return $fileMapFilter;
    }
}
