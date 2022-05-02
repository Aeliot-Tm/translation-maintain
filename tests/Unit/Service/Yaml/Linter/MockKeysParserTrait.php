<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysParser;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

trait MockKeysParserTrait
{
    /**
     * @param array<string,array<string,mixed>> $returns
     *
     * @return MockObject&KeysParser
     */
    private function mockKeysParser(array $returns, TestCase $testCase): MockObject
    {
        $fileMapFilter = $testCase->getMockBuilder(KeysParser::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        foreach ($returns as $method => $value) {
            $fileMapFilter->method($method)->willReturn($value);
        }

        return $fileMapFilter;
    }
}
