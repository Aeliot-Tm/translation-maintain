<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportLineInterface;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\SameValueLinter;
use PHPUnit\Framework\TestCase;

final class SameValueLinterTest extends TestCase
{
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

        self::assertSame(
            $expected,
            array_map(static fn (ReportLineInterface $x): array => $x->jsonSerialize(), $bag->getLines())
        );
    }

    public function getDataForTestCorrectFiles(): \Generator
    {
        yield [[], []];
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

    private function createLinter(array $filesMap, array $fileTranslations): SameValueLinter
    {
        $fileManipulator = $this->getMockBuilder(FileToSingleLevelArrayParser::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $method = $fileManipulator->method('parse');
        foreach ($fileTranslations as $file => $translations) {
            $method->with($file)->willReturn($translations);
        }

        $fileMapFilter = $this->getMockBuilder(FileMapFilter::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $fileMapFilter->method('getFilesMap')->willReturn($filesMap);

        return new SameValueLinter($fileMapFilter, $fileManipulator);
    }
}
