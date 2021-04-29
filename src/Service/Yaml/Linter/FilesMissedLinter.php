<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Model\FilesMissedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesMapProvider;

final class FilesMissedLinter implements LinterInterface
{
    private FilesMapProvider $filesMapProvider;

    public function __construct(FilesMapProvider $filesMapProvider)
    {
        $this->filesMapProvider = $filesMapProvider;
    }

    public function getKey(): string
    {
        return 'files_missed';
    }

    public function lint(): ReportBag
    {
        $bag = new ReportBag(FilesMissedLine::class);
        $filesMap = $this->filesMapProvider->getFilesMap();
        $mentionedLocales = $this->getMentionedLocales($filesMap);
        foreach ($filesMap as $domain => $locales) {
            if ($omitted = array_diff($mentionedLocales, array_keys($locales))) {
                $bag->addLine(new FilesMissedLine($domain, $omitted));
            }
        }

        return $bag;
    }

    private function getMentionedLocales(array $filesMap): array
    {
        $mentionedLocales = array_unique(
            array_merge(...array_map(static fn(array $x): array => array_keys($x), array_values($filesMap)))
        );
        sort($mentionedLocales);

        return $mentionedLocales;
    }
}
