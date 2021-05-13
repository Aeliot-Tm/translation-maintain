<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\KeysPatternLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysParser;

final class KeyPatternLinter implements LinterInterface
{
    private FilesFinder $filesFinder;
    private KeysParser $keysParser;
    private ?string $keyPattern;

    public function __construct(FilesFinder $filesFinder, KeysParser $keysParser, ?string $yamlKeyPattern)
    {
        $this->filesFinder = $filesFinder;
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

        $bag = new ReportBag(KeysPatternLine::class);
        $domainsFiles = $this->filesFinder->getFilesMap();
        foreach ($domainsFiles as $domain => $localesFiles) {
            if ($filterDto->domains && !\in_array($domain, $filterDto->domains, true)) {
                continue;
            }

            if ($filterDto->locales) {
                $localesFiles = array_intersect_key($localesFiles, array_flip($filterDto->locales));
            }

            $keys = $this->getKeysSummary($this->keysParser->getParsedKeys($localesFiles));
            foreach ($keys as $languageId => $locales) {
                if (!preg_match($this->keyPattern, $languageId)) {
                    $bag->addLine(new KeysPatternLine($domain, $languageId, $locales));
                }
            }
        }

        return $bag;
    }

    private function getKeysSummary(array $parsedKeys): array
    {
        $summary = [];
        foreach ($parsedKeys as $locale => $languageIds) {
            foreach ($languageIds as $languageId) {
                if (!array_key_exists($languageId, $summary)) {
                    $summary[$languageId] = [];
                }
                $summary[$languageId][] = $locale;
            }
        }
        asort($summary);

        return $summary;
    }
}
