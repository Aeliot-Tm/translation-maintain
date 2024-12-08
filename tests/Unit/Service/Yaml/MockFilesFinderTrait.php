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

use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;
use PHPUnit\Framework\TestCase;

trait MockFilesFinderTrait
{
    /**
     * @param array<string,array<string,array<int,string>>> $filesMap
     */
    private function mockFilesFinder(array $filesMap, TestCase $testCase): FilesFinder
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
