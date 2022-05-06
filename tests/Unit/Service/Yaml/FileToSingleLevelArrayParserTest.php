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

    /**
     * @dataProvider getDataForTestParseFiles
     *
     * @param array<string,string> $expected
     * @param string[] $files
     * @param array<string,mixed> $fileTranslations
     */
    public function testParseFiles(array $expected, array $files, array $fileTranslations): void
    {
        self::assertSame($expected, $this->createParser($fileTranslations)->parseFiles($files));
    }

    public function getDataForTestParse(): \Generator
    {
        yield [
            ['a.b' => '*', 'a.c' => '*', 'a.d.e' => '*'],
            ['a' => ['b' => '*', 'c' => '*', 'd' => ['e' => '*']]],
        ];
    }

    public function getDataForTestParseFiles(): \Generator
    {
        yield [
            ['a' => '*', 'b' => '*', 'c' => '*'],
            ['/var/a/message.en.yaml', '/var/b/message.en.yaml'],
            [
                '/var/a/message.en.yaml' => ['a' => '*', 'c' => '*'],
                '/var/b/message.en.yaml' => ['b' => '*'],
            ],
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
