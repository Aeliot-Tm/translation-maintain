<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\SameValueLinter;
use Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\MockFileToSingleLevelArrayParserTrait;
use PHPUnit\Framework\TestCase;

final class SameValueLinterTest extends TestCase
{
    use ConvertReportBagToArrayTrait;
    use MockFileMapFilterTrait;
    use MockFileToSingleLevelArrayParserTrait;

    /**
     * @dataProvider getDataForTestCorrectFiles
     *
     * @param array<string,array<string,array<int,string>>> $filesMap
     * @param array<string,mixed> $fileTranslations
     */
    public function testNothingDetected(array $filesMap, array $fileTranslations): void
    {
        $linter = $this->createLinter($filesMap, $fileTranslations);
        $bag = $linter->lint(new LintYamlFilterDto());
        self::assertTrue($bag->isEmpty());
    }

    /**
     * @dataProvider getDataForTestFilesWithSameValues
     *
     * @param array<string,array<string,array<int,string>>> $filesMap
     * @param array<string,mixed> $fileTranslations
     */
    public function testSameValuesDetected(array $expected, array $filesMap, array $fileTranslations): void
    {
        $linter = $this->createLinter($filesMap, $fileTranslations);
        $bag = $linter->lint(new LintYamlFilterDto());

        self::assertSame($expected, $this->convertReportBagToArrayTrait($bag));
    }

    public function getDataForTestCorrectFiles(): \Generator
    {
        yield [
            ['messages' => ['en' => ['messages.en.yaml']]],
            ['messages.en.yaml' => ['key.a' => 'value_a', 'key.b' => 'value_b']],
        ];
    }

    public function getDataForTestFilesWithSameValues(): \Generator
    {
        yield [
            [
                [
                    'domain' => 'messages',
                    'locale' => 'en',
                    'translation' => 'value_a',
                    'translation_ids' => 'a.b, a.c',
                ],
            ],
            ['messages' => ['en' => ['messages.en.yaml']]],
            ['messages.en.yaml' => ['a.c' => 'value_a', 'a.b' => 'value_a', 'a' => 'a', 'b' => 'b']],
        ];
    }

    /**
     * @param array<string,array<string,array<int,string>>> $filesMap
     * @param array<string,mixed> $fileTranslations
     */
    private function createLinter(array $filesMap, array $fileTranslations): SameValueLinter
    {
        $fileMapFilter = $this->mockFileMapFilter($filesMap, $this);
        $fileParser = $this->mockFileToSingleLevelArrayParser($fileTranslations, $this);

        return new SameValueLinter($fileMapFilter, $fileParser);
    }
}
