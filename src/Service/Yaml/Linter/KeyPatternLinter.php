<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysParser;

final class KeyPatternLinter implements LinterInterface
{
    private FileMapFilter $fileMapFilter;
    private KeysParser $keysParser;
    private ?string $keyPattern;

    public function __construct(FileMapFilter $fileMapFilter, KeysParser $keysParser, ?string $yamlKeyPattern)
    {
        $this->fileMapFilter = $fileMapFilter;
        $this->keysParser = $keysParser;
        $this->keyPattern = $yamlKeyPattern;
    }

    public function getKey(): string
    {
        return 'key_pattern';
    }

    public function getPresets(): array
    {
        return [];
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        if (!$this->keyPattern) {
            throw new \LogicException('YAML Key Pattern Linter is not configured yet');
        }

        $bag = $this->createReportBag();
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);
        foreach ($domainsFiles as $domain => $localesFiles) {
            $keys = $this->getKeysSummary($this->keysParser->getParsedKeys($localesFiles));
            foreach ($keys as $translationId => $locales) {
                if (!preg_match($this->keyPattern, $translationId)) {
                    $bag->addLine($domain, $translationId, $locales);
                }
            }
        }

        return $bag;
    }

    private function createReportBag(): ReportBag
    {
        return new ReportBag(
            [
                'domain' => ['string'],
                'invalid_translation_id' => ['string'],
                'locales' => ['array'],
            ],
            'All translation keys match configured pattern',
            'Translation keys that are not match configured pattern'
        );
    }

    private function getKeysSummary(array $parsedKeys): array
    {
        $summary = [];
        foreach ($parsedKeys as $locale => $translationIds) {
            foreach ($translationIds as $translationId) {
                if (!\array_key_exists($translationId, $summary)) {
                    $summary[$translationId] = [];
                }
                $summary[$translationId][] = $locale;
            }
        }
        asort($summary);

        return $summary;
    }
}
