<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Model\KeysDuplicatedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesMapProvider;

final class KeysDuplicatedLinter implements LinterInterface
{
    use GlueKeysTrait;

    private FileManipulator $fileManipulator;
    private FilesMapProvider $filesMapProvider;

    public function __construct(FileManipulator $fileManipulator, FilesMapProvider $filesMapProvider)
    {
        $this->fileManipulator = $fileManipulator;
        $this->filesMapProvider = $filesMapProvider;
    }

    public function getKey(): string
    {
        return 'keys_duplicated';
    }

    public function lint(): ReportBag
    {
        $bag = new ReportBag(KeysDuplicatedLine::class);
        $domainsFiles = $this->filesMapProvider->getFilesMap();

        foreach ($domainsFiles as $domain => $localesFiles) {
            foreach ($localesFiles as $locale => $files) {
                $values = [];
                $duplicatedKeys = [];
                foreach ($files as $file) {
                    foreach ($this->glueKeys($this->fileManipulator->parse($file)) as $languageId => $value) {
                        if (array_key_exists($languageId, $values)) {
                            $duplicatedKeys[] = $languageId;
                        } else {
                            $values[$languageId] = $value;
                        }
                    }
                }

                $duplicatedKeys = array_unique($duplicatedKeys);
                sort($duplicatedKeys);

                foreach ($duplicatedKeys as $languageId) {
                    $bag->addLine(new KeysDuplicatedLine($domain, $locale, $languageId));
                }
            }
        }

        return $bag;
    }
}
