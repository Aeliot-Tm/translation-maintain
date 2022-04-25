<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\FilesMissedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;

final class FilesMissedLinter implements LinterInterface
{
    private FilesFinder $filesFinder;
    private FileMapFilter $fileMapFilter;

    public function __construct(FilesFinder $filesFinder, FileMapFilter $fileMapFilter)
    {
        $this->filesFinder = $filesFinder;
        $this->fileMapFilter = $fileMapFilter;
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
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);
        $mentionedLocales = $this->filesFinder->getLocales();
        foreach ($domainsFiles as $domain => $localesFiles) {
            if ($omittedLocales = array_diff($mentionedLocales, array_keys($localesFiles))) {
                $bag->addLine(new FilesMissedLine($domain, $omittedLocales));
            }
        }

        return $bag;
    }
}
