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
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\KeysDuplicatedLinter;
use Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\MockFileToSingleLevelArrayParserTrait;
use PHPUnit\Framework\TestCase;

final class KeysDuplicatedLinterTest extends TestCase
{
    use ConvertReportBagToArrayTrait;
    use MockFileMapFilterTrait;
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
                    'locale' => 'en',
                    'duplicated_translation_id' => 'b',
                ],
            ],
            ['messages' => ['en' => ['/var/a/messages.en.yaml', '/var/b/messages.en.yaml']]],
            [
                '/var/a/messages.en.yaml' => ['a' => '*', 'b' => '*', 'c' => '*'],
                '/var/b/messages.en.yaml' => ['b' => '*'],
            ],
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
    private function createLinter(array $filesMap, array $fileTranslations): KeysDuplicatedLinter
    {
        $fileMapFilter = $this->mockFileMapFilter($filesMap, $this);
        $fileParser = $this->mockFileToSingleLevelArrayParser($fileTranslations, $this);

        return new KeysDuplicatedLinter($fileMapFilter, $fileParser);
    }
}
