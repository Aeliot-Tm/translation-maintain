<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysLinker;
use PHPUnit\Framework\TestCase;

final class FileToSingleLevelArrayParserTest extends TestCase
{
    use MockFileManipulatorTrait;

    /**
     * @dataProvider getDataForTestParse
     *
     * @param array<string,string> $expected
     * @param array<string,array<string,array<int,string>>> $value
     */
    public function testParse(array $expected, array $value): void
    {
        self::assertSame($expected, $this->createParser($value)->parse('some_path'));
    }

    public function getDataForTestParse(): \Generator
    {
        yield [
            ['a.b' => '*', 'a.c' => '*', 'a.d.e' => '*'],
            ['a' => ['b' => '*', 'c' => '*', 'd' => ['e' => '*']]],
        ];
    }

    /**
     * @param array<string,array<string,array<int,string>>> $value
     */
    private function createParser(array $value): FileToSingleLevelArrayParser
    {
        return new FileToSingleLevelArrayParser($this->mockFileManipulatorSingle($value, $this), new KeysLinker());
    }
}
