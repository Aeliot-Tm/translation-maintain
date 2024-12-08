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

namespace Aeliot\Bundle\TransMaintain\Service;

final class DirectoryProvider
{
    private string $defaultPath;
    /**
     * @var array<int,string>
     */
    private array $dirs;

    /**
     * @param iterable<int,string> $dirs
     */
    public function __construct(string $defaultPath, iterable $dirs)
    {
        $this->defaultPath = $defaultPath;
        $dirs = $dirs instanceof \Traversable ? iterator_to_array($dirs) : (array) $dirs;
        array_unshift($dirs, $defaultPath);
        $this->dirs = array_unique($dirs);
    }

    /**
     * @return array<int,string>
     */
    public function getAll(): array
    {
        return $this->dirs;
    }

    public function getDefault(): string
    {
        return $this->defaultPath;
    }
}
