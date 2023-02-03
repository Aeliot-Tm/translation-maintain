<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

trait MockFileManipulatorTrait
{
    private function createFileManipulatorMock(TestCase $testCase): FileManipulator
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
     */
    private function mockFileManipulatorSingle(array $value, TestCase $testCase): FileManipulator
    {
        /** @var MockObject $fileMapFilter */
        $fileMapFilter = $this->createFileManipulatorMock($testCase);
        $fileMapFilter->method('parse')->willReturn($value);

        /** @var FileManipulator $fileMapFilter */
        return $fileMapFilter;
    }

    /**
     * @param array<string,array<string,mixed>> $fileTranslations
     */
    private function mockFileManipulatorMultiple(array $fileTranslations, TestCase $testCase): FileManipulator
    {
        /** @var MockObject $fileMapFilter */
        $fileMapFilter = $this->createFileManipulatorMock($testCase);
        $fileMapFilter->method('parse')->willReturnCallback(function (string $path) use ($fileTranslations) {
            return $fileTranslations[$path];
        });

        /** @var FileManipulator $fileMapFilter */
        return $fileMapFilter;
    }
}
