<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\FilesMissedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;
use Aeliot\Bundle\TransMaintain\Service\Yaml\LinterRegistry;

final class FilesMissedLinter implements LinterInterface
{
    private FilesFinder $filesFinder;

    public function __construct(FilesFinder $filesFinder)
    {
        $this->filesFinder = $filesFinder;
    }

    public function getKey(): string
    {
        return 'files_missed';
    }

    public function getPresets(): array
    {
        return [LinterInterface::PRESET_BASE];
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        $bag = new ReportBag(FilesMissedLine::class);
        $filesMap = $this->filesFinder->getFilesMap();
        $mentionedLocales = $this->filesFinder->getLocales();
        foreach ($filesMap as $domain => $localesFiles) {
            if ($filterDto->domains && !\in_array($domain, $filterDto->domains, true)) {
                continue;
            }
            $omitted = array_diff($mentionedLocales, array_keys($localesFiles));
            if ($filterDto->locales) {
                $omitted = \array_intersect($omitted, $filterDto->locales);
            }
            if ($omitted) {
                $bag->addLine(new FilesMissedLine($domain, $omitted));
            }
        }

        return $bag;
    }
}
