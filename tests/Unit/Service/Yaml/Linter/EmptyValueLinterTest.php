<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\EmptyValueLinter;
use Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\MockFileToSingleLevelArrayParserTrait;
use PHPUnit\Framework\TestCase;

final class EmptyValueLinterTest extends TestCase
{
    use ConvertReportBagToArrayTrait;
    use MockFileMapFilterTrait;
    use MockFileToSingleLevelArrayParserTrait;

    /**
     * @dataProvider getDataForTestDetected
     *
     * @param array<string,array<string,array<int,string>>> $filesMap
     * @param array<string,mixed> $fileTranslations
     */
    public function testDetected(array $expected, array $filesMap, array $fileTranslations): void
    {
        $linter = $this->createLinter($filesMap, $fileTranslations);
        $bag = $linter->lint(new LintYamlFilterDto());

        self::assertSame($expected, $this->convertReportBagToArrayTrait($bag));
    }

    /**
     * @dataProvider getDataForTestNothingDetected
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

    public function getDataForTestDetected(): \Generator
    {
        yield [
            [
                [
                    'domain' => 'messages',
                    'translation_id' => 'a',
                    'locales' => 'en',
                ],
                [
                    'domain' => 'messages',
                    'translation_id' => 'c',
                    'locales' => 'en',
                ],
            ],
            ['messages' => ['en' => ['messages.en.yaml']]],
            ['messages.en.yaml' => ['a' => '', 'b' => '*', 'c' => '']],
        ];
    }

    public function getDataForTestNothingDetected(): \Generator
    {
        yield [
            ['messages' => ['en' => ['messages.en.yaml']]],
            ['messages.en.yaml' => ['a' => '*', 'b' => '*', 'c' => '*']],
        ];
    }

    /**
     * @param array<string,array<string,array<int,string>>> $filesMap
     * @param array<string,mixed> $fileTranslations
     */
    private function createLinter(array $filesMap, array $fileTranslations): EmptyValueLinter
    {
        $fileMapFilter = $this->mockFileMapFilter($filesMap, $this);
        $fileParser = $this->mockFileToSingleLevelArrayParser($fileTranslations, $this);

        return new EmptyValueLinter($fileMapFilter, $fileParser);
    }
}
