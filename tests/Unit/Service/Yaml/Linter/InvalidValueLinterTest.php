<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportLineInterface;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\InvalidValueLinter;
use PHPUnit\Framework\TestCase;

final class InvalidValueLinterTest extends TestCase
{
    use MockFileMapFilterTrait;
    use MockFileToSingleLevelArrayParserTrait;

    /**
     * @dataProvider getDataForTestFilesWithSameValues
     *
     * @param array<string,array<string,array<int,string>>> $filesMap
     * @param array<string,mixed> $fileTranslations
     */
    public function testSameValuesDetected(
        array $expected,
        array $filesMap,
        array $fileTranslations,
        string $pattern
    ): void {
        $linter = $this->createLinter($filesMap, $fileTranslations, $pattern);
        $bag = $linter->lint(new LintYamlFilterDto());

        self::assertSame(
            $expected,
            array_map(static fn (ReportLineInterface $x): array => $x->jsonSerialize(), $bag->getLines())
        );
    }

    public function getDataForTestFilesWithSameValues(): \Generator
    {
        yield [
            [
                [
                    'domain' => 'messages',
                    'locale' => 'en',
                    'translation_id' => 'invalid_key',
                ],
            ],
            ['messages' => ['en' => ['messages.en.yaml']]],
            ['messages.en.yaml' => ['valid_key' => 'value_a', 'invalid_key' => 'value of '.\chr(160)]],
            '/[\xa0]/',
        ];
    }

    /**
     * @param array<string,array<string,array<int,string>>> $filesMap
     * @param array<string,mixed> $fileTranslations
     */
    private function createLinter(array $filesMap, array $fileTranslations, string $pattern): InvalidValueLinter
    {
        $fileMapFilter = $this->mockFileMapFilter($filesMap, $this);
        $fileParser = $this->mockFileToSingleLevelArrayParser($fileTranslations, $this);

        return new InvalidValueLinter($fileParser, $fileMapFilter, $pattern);
    }
}
