<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\FilesTransformedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileTransformedStateDetector;

final class FileTransformedLinter implements LinterInterface
{
    private FileMapFilter $fileMapFilter;
    private FileTransformedStateDetector $fileTransformedStateDetector;

    public function __construct(
        FileMapFilter $fileMapFilter,
        FileTransformedStateDetector $fileTransformedStateDetector
    ) {
        $this->fileMapFilter = $fileMapFilter;
        $this->fileTransformedStateDetector = $fileTransformedStateDetector;
    }

    public function getKey(): string
    {
        return 'file_transformed';
    }

    /**
     * @return string[]
     */
    public function getPresets(): array
    {
        return [];
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        $bag = new ReportBag(FilesTransformedLine::class);
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);

        foreach ($domainsFiles as $domain => $localesFiles) {
            foreach ($localesFiles as $locale => $files) {
                foreach ($files as $file) {
                    if (!$this->fileTransformedStateDetector->isTransformed($file)) {
                        $bag->addLine(new FilesTransformedLine($domain, $locale, $file));
                    }
                }
            }
        }

        return $bag;
    }
}
