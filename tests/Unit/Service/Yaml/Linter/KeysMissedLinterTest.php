<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysParser;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\KeysMissedLinter;
use Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\MockFilesFinderTrait;
use Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\MockFileToSingleLevelArrayParserTrait;
use PHPUnit\Framework\TestCase;

final class KeysMissedLinterTest extends TestCase
{
    use ConvertReportBagToArrayTrait;
    use MockFilesFinderTrait;
    use MockFileToSingleLevelArrayParserTrait;

    /**
     * @dataProvider getDataForTestDetected
     *
     * @param array<array<string,string>> $expected
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
                    'translation_id' => 'b',
                    'omitted_locales' => 'fr',
                ],
            ],
            [
                'messages' => [
                    'en' => ['messages.en.yaml'],
                    'fr' => ['messages.fr.yaml'],
                ],
            ],
            [
                'messages.en.yaml' => ['a' => '*', 'b' => '*', 'c' => '*'],
                'messages.fr.yaml' => ['a' => '*', 'c' => '*'],
            ],
        ];
    }

    public function getDataForTestNothingDetected(): \Generator
    {
        yield [
            [
                'messages' => [
                    'en' => ['messages.en.yaml'],
                    'fr' => ['messages.fr.yaml'],
                ],
            ],
            [
                'messages.en.yaml' => ['a' => '*', 'b' => '*', 'c' => '*'],
                'messages.fr.yaml' => ['a' => '*', 'b' => '*', 'c' => '*'],
            ],
        ];
    }

    /**
     * @param array<string,array<string,array<int,string>>> $filesMap
     * @param array<string,mixed> $fileTranslations
     */
    private function createLinter(array $filesMap, array $fileTranslations): KeysMissedLinter
    {
        $fileMapFilter = $this->mockFilesFinder($filesMap, $this);
        $keysParser = new KeysParser($this->mockFileToSingleLevelArrayParser($fileTranslations, $this));

        return new KeysMissedLinter($fileMapFilter, $keysParser);
    }
}
