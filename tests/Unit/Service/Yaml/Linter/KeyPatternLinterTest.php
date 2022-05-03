<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\KeyPatternLinter;
use PHPUnit\Framework\TestCase;

final class KeyPatternLinterTest extends TestCase
{
    use ConvertReportBagToArrayTrait;
    use MockFileMapFilterTrait;
    use MockKeysParserTrait;

    /**
     * @dataProvider getDataForTestDetected
     *
     * @param array<string,array<string,array<int,string>>> $filesMap
     * @param array<string,array<string,mixed>> $returns
     */
    public function testDetected(array $expected, array $filesMap, array $returns, string $pattern): void
    {
        $linter = $this->createLinter($filesMap, $returns, $pattern);
        $bag = $linter->lint(new LintYamlFilterDto());
        self::assertSame($expected, $this->convertReportBagToArrayTrait($bag));
    }

    /**
     * @dataProvider getDataForTestNothingDetected
     *
     * @param array<string,array<string,array<string>>> $filesMap
     * @param array<string,array<string,mixed>> $returns
     */
    public function testNothingDetected(array $filesMap, array $returns, string $pattern): void
    {
        $linter = $this->createLinter($filesMap, $returns, $pattern);
        $bag = $linter->lint(new LintYamlFilterDto());
        self::assertTrue($bag->isEmpty());
    }

    public function getDataForTestDetected(): \Generator
    {
        yield [
            [
                [
                    'domain' => 'messages',
                    'invalid_translation_id' => '\b',
                    'locales' => 'en',
                ],
                [
                    'domain' => 'messages',
                    'invalid_translation_id' => 'c+d',
                    'locales' => 'en',
                ],
            ],
            ['messages' => ['en' => ['messages.en.yaml']]],
            ['getParsedKeys' => ['en' => ['a', '\b', 'c+d']]],
            '/^[a-zA-Z0-9_.-]+$/',
        ];
    }

    public function getDataForTestNothingDetected(): \Generator
    {
        yield [
            ['messages' => ['en' => ['messages.en.yaml']]],
            ['getParsedKeys' => ['en' => ['a', 'a.b', 'a.b.c']]],
            '/^[a-zA-Z0-9_.-]+$/',
        ];
    }

    /**
     * @param array<string,array<string,array<string>>> $filesMap
     * @param array<string,array<string,mixed>> $returns
     */
    private function createLinter(array $filesMap, array $returns, string $pattern): KeyPatternLinter
    {
        $fileMapFilter = $this->mockFileMapFilter($filesMap, $this);
        $localesDetector = $this->mockKeysParser($returns, $this);

        return new KeyPatternLinter($fileMapFilter, $localesDetector, $pattern);
    }
}
