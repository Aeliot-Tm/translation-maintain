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
