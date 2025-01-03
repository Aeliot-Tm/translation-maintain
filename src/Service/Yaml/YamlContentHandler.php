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

use Symfony\Component\Yaml\Yaml;

final class YamlContentHandler
{
    private int $yamlIndent;

    public function __construct(int $yamlIndent)
    {
        $this->yamlIndent = $yamlIndent;
    }

    /**
     * @param array<string,mixed> $yaml
     */
    public function dump(array $yaml): string
    {
        // THINK: how to escape single words?
        $dumpFlags = Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK;

        return Yaml::dump($yaml, 100, $this->yamlIndent, $dumpFlags);
    }

    /**
     * @return array<string,mixed>|null
     */
    public function parseFile(string $filename): ?array
    {
        /*
         * NOTE: don't use Yaml::PARSE_CONSTANT like in {@see \Symfony\Component\Translation\Loader\YamlFileLoader::loadResource()}
         *       to leave values as is
         */
        return Yaml::parseFile($filename, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
    }
}
