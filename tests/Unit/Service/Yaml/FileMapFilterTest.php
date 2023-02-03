<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use PHPUnit\Framework\TestCase;

final class FileMapFilterTest extends TestCase
{
    use MockFilesFinderTrait;

    /**
     * @dataProvider getDataForFiltering
     *
     * @param array<string,array<string,array<int,string>>> $expected
     * @param array<string,array<string,array<int,string>>> $filesMap
     */
    public function testFiltering(array $expected, array $filesMap, LintYamlFilterDto $filterDto): void
    {
        self::assertSame($expected, $this->createFileMapFilter($filesMap)->getFilesMap($filterDto));
    }

    public function getDataForFiltering(): \Generator
    {
        yield [
            ['domain_A' => ['en' => ['domain_A.en.yaml']]],
            ['domain_A' => ['en' => ['domain_A.en.yaml']]],
            new LintYamlFilterDto(),
        ];

        $dto = new LintYamlFilterDto();
        $dto->locales = ['en', 'fr'];
        yield [
            ['domain_A' => ['en' => ['domain_A.en.yaml'], 'fr' => ['domain_A.fr.yaml']]],
            ['domain_A' => ['de' => ['domain_A.de.yaml'], 'en' => ['domain_A.en.yaml'], 'fr' => ['domain_A.fr.yaml']]],
            $dto,
        ];

        $dto = new LintYamlFilterDto();
        $dto->domains = ['domain_A', 'domain_C'];
        yield [
            [
                'domain_A' => ['en' => ['domain_A.en.yaml']],
                'domain_C' => ['en' => ['domain_C.en.yaml']],
            ],
            [
                'domain_A' => ['en' => ['domain_A.en.yaml']],
                'domain_B' => ['en' => ['domain_B.en.yaml']],
                'domain_C' => ['en' => ['domain_C.en.yaml']],
            ],
            $dto,
        ];

        $dto = new LintYamlFilterDto();
        $dto->domains = ['domain_A', 'domain_C'];
        $dto->locales = ['de', 'fr'];
        yield [
            [
                'domain_A' => ['de' => ['domain_A.en.yaml'], 'fr' => ['domain_A.fr.yaml']],
                'domain_C' => ['de' => ['domain_C.en.yaml'], 'fr' => ['domain_C.fr.yaml']],
            ],
            [
                'domain_A' => ['de' => ['domain_A.en.yaml'], 'en' => ['domain_A.en.yaml'], 'fr' => ['domain_A.fr.yaml']],
                'domain_B' => ['de' => ['domain_B.en.yaml'], 'en' => ['domain_B.en.yaml'], 'fr' => ['domain_B.fr.yaml']],
                'domain_C' => ['de' => ['domain_C.en.yaml'], 'en' => ['domain_C.en.yaml'], 'fr' => ['domain_C.fr.yaml']],
            ],
            $dto,
        ];
    }

    /**
     * @param array<string,array<string,array<int,string>>> $filesMap
     */
    private function createFileMapFilter(array $filesMap): FileMapFilter
    {
        return new FileMapFilter($this->mockFilesFinder($filesMap, $this));
    }
}
