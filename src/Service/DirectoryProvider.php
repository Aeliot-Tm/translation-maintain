<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service;

final class DirectoryProvider
{
    private string $defaultPath;
    /**
     * @var array<string>
     */
    private array $dirs;

    public function __construct(string $defaultPath, iterable $dirs)
    {
        $this->defaultPath = $defaultPath;
        $dirs = $dirs instanceof \Traversable ? iterator_to_array($dirs) : (array) $dirs;
        array_unshift($dirs, $defaultPath);
        $this->dirs = array_unique($dirs);
    }

    /**
     * @return array<string>
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
