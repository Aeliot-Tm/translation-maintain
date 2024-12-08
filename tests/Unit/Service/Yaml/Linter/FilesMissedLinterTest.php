<?php

declare(strict_types=1);

/*
 * This file is part of the TransMaintain.
 *
 * (c) Anatoliy Melnikov <5785276@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Service\LocalesDetector;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\FilesMissedLinter;
use PHPUnit\Framework\TestCase;

final class FilesMissedLinterTest extends TestCase
{
    use ConvertReportBagToArrayTrait;
    use MockFileMapFilterTrait;

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
     * @param array<string,array<string,array<string>>> $filesMap
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
                    'omitted_locales' => 'fr',
                ],
            ],
            ['messages' => ['en' => ['messages.en.yaml']]],
            ['en', 'fr'],
        ];
        yield [
            [
                [
                    'domain' => 'messages',
                    'omitted_locales' => 'de, fr',
                ],
            ],
            ['messages' => ['en' => ['messages.en.yaml']]],
            ['en', 'fr', 'de'],
        ];
    }

    public function getDataForTestNothingDetected(): \Generator
    {
        yield [
            ['messages' => ['en' => ['messages.en.yaml']]],
            ['en'],
        ];
    }

    /**
     * @param array<string,array<string,array<string>>> $filesMap
     * @param string[] $locales
     */
    private function createLinter(array $filesMap, array $locales): FilesMissedLinter
    {
        $fileMapFilter = $this->mockFileMapFilter($filesMap, $this);
        $localesDetector = $this->mockLocalesDetector($locales, $this);

        return new FilesMissedLinter($fileMapFilter, $localesDetector);
    }

    /**
     * @param string[] $locales
     */
    private function mockLocalesDetector(array $locales, TestCase $testCase): LocalesDetector
    {
        $localesDetector = $testCase->getMockBuilder(LocalesDetector::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $localesDetector->method('getLocales')->willReturn($locales);

        return $localesDetector;
    }
}
