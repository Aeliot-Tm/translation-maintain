<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\FilesTransformedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\TransformationConveyor;
use Aeliot\Bundle\TransMaintain\Service\Yaml\YamlContentHandler;

final class FileTransformedLinter implements LinterInterface
{
    private FileMapFilter $fileMapFilter;
    private TransformationConveyor $transformationConveyor;
    private YamlContentHandler $yamlContentHandler;

    public function __construct(
        FileMapFilter $fileMapFilter,
        TransformationConveyor $transformationConveyor,
        YamlContentHandler $yamlContentHandler
    ) {
        $this->fileMapFilter = $fileMapFilter;
        $this->transformationConveyor = $transformationConveyor;
        $this->yamlContentHandler = $yamlContentHandler;
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
                    if (!$this->isTransformed($file)) {
                        $bag->addLine(new FilesTransformedLine($domain, $locale, $file));
                    }
                }
            }
        }

        return $bag;
    }

    private function isTransformed(string $file): bool
    {
        if (!$yaml = $this->yamlContentHandler->parseFile($file)) {
            return false;
        }

        $yaml = $this->transformationConveyor->transform($yaml);

        return $this->yamlContentHandler->dump($yaml) === file_get_contents($file);
    }
}
