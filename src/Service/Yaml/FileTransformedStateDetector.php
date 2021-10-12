<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

final class FileTransformedStateDetector
{
    private TransformationConveyor $transformationConveyor;
    private YamlContentHandler $yamlContentHandler;

    public function __construct(TransformationConveyor $transformationConveyor, YamlContentHandler $yamlContentHandler)
    {
        $this->transformationConveyor = $transformationConveyor;
        $this->yamlContentHandler = $yamlContentHandler;
    }

    public function isTransformed(string $file): bool
    {
        if (!$yaml = $this->yamlContentHandler->parseFile($file)) {
            return false;
        }

        $yaml = $this->transformationConveyor->transform($yaml);

        return $this->yamlContentHandler->dump($yaml) === file_get_contents($file);
    }
}
