<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysParser;
use PHPUnit\Framework\TestCase;

final class KeysParserTest extends TestCase
{
    use MockFileToSingleLevelArrayParserTrait;

    /**
     * @dataProvider getDataForTestGetOmittedKeys
     *
     * @param array<string,array<string>> $expected
     * @param array<string,array<string>> $localesKeys
     */
    public function testGetOmittedKeys(array $expected, array $localesKeys): void
    {
        $fileToSingleLevelArrayParser = $this->mockFileToSingleLevelArrayParser([], $this);
        self::assertSame($expected, (new KeysParser($fileToSingleLevelArrayParser))->getOmittedKeys($localesKeys));
    }

    /**
     * @dataProvider getDataForTestGetParsedKeys
     *
     * @param array<string,array<string>> $expected
     * @param array<string,array<string>> $localesFiles
     * @param array<string,mixed> $fileTranslations
     */
    public function testGetParsedKeys(array $expected, array $localesFiles, array $fileTranslations): void
    {
        $fileToSingleLevelArrayParser = $this->mockFileToSingleLevelArrayParser($fileTranslations, $this);
        self::assertSame($expected, (new KeysParser($fileToSingleLevelArrayParser))->getParsedKeys($localesFiles));
    }

    /**
     * @dataProvider getDataForTestMergeKeys
     *
     * @param array<string> $expected
     * @param array<string,array<string>> $localesKeys
     */
    public function testMergeKeys(array $expected, array $localesKeys): void
    {
        $fileToSingleLevelArrayParser = $this->mockFileToSingleLevelArrayParser([], $this);
        self::assertSame($expected, (new KeysParser($fileToSingleLevelArrayParser))->mergeKeys($localesKeys));
    }

    public function getDataForTestGetOmittedKeys(): \Generator
    {
        yield [
            ['en' => [], 'fr' => ['b']],
            ['en' => ['a', 'b'], 'fr' => ['a']],
        ];
    }

    public function getDataForTestGetParsedKeys(): \Generator
    {
        yield [
            ['en' => ['a', 'b', 'c']],
            ['en' => ['/var/a/message.en.yaml', '/var/b/message.en.yaml']],
            [
                '/var/a/message.en.yaml' => ['a' => '*', 'c' => '*'],
                '/var/b/message.en.yaml' => ['b' => '*'],
            ],
        ];

        yield [
            ['en' => ['a', 'c'], 'fr' => ['b']],
            ['en' => ['message.en.yaml'], 'fr' => ['message.fr.yaml']],
            [
                'message.en.yaml' => ['a' => '*', 'c' => '*'],
                'message.fr.yaml' => ['b' => '*'],
            ],
        ];
    }

    public function getDataForTestMergeKeys(): \Generator
    {
        yield [
            ['a', 'b', 'f'],
            ['en' => ['a', 'b'], 'fr' => ['a', 'f']],
        ];
    }
}
