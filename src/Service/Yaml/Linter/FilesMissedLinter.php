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

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\LocalesDetector;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;

final class FilesMissedLinter implements LinterInterface
{
    private FileMapFilter $fileMapFilter;
    private LocalesDetector $localesDetector;

    public function __construct(FileMapFilter $fileMapFilter, LocalesDetector $localesDetector)
    {
        $this->fileMapFilter = $fileMapFilter;
        $this->localesDetector = $localesDetector;
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
        $bag = $this->createReportBag();
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);
        $mentionedLocales = $this->localesDetector->getLocales();
        foreach ($domainsFiles as $domain => $localesFiles) {
            if ($omittedLocales = array_diff($mentionedLocales, array_keys($localesFiles))) {
                $bag->addLine($domain, $omittedLocales);
            }
        }

        return $bag;
    }

    private function createReportBag(): ReportBag
    {
        return new ReportBag(
            [
                'domain' => ['string'],
                'omitted_locales' => ['array'],
            ],
            'All domains have files for all used locales',
            'Missed locales files for domains'
        );
    }
}
