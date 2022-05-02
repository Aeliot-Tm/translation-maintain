<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

trait MockFileManipulatorTrait
{
    /**
     * @return MockObject&FileManipulator
     */
    private function createFileManipulatorMock(TestCase $testCase): MockObject
    {
        return $testCase->getMockBuilder(FileManipulator::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }

    /**
     * @param array<string,array<string,array<int,string>>> $value
     *
     * @return MockObject&FileManipulator
     */
    private function mockFileManipulatorSingle(array $value, TestCase $testCase): MockObject
    {
        $fileMapFilter = $this->createFileManipulatorMock($testCase);
        $fileMapFilter->method('parse')->willReturn($value);

        return $fileMapFilter;
    }
}
