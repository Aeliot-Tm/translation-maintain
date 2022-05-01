<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;

final class LocalesDetector
{
    private FilesFinder $filesFinder;

    public function __construct(FilesFinder $filesFinder)
    {
        $this->filesFinder = $filesFinder;
    }

    /**
     * @return array<int,string>
     */
    public function getLocales(): array
    {
        $mentionedLocales = array_unique(
            array_merge(...array_map('array_keys', array_values($this->filesFinder->getFilesMap())))
        );

        sort($mentionedLocales);

        return $mentionedLocales;
    }
}
