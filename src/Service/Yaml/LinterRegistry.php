<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\LinterInterface;

final class LinterRegistry
{
    private const RESERVED_KEYS = ['all', 'base'];

    /**
     * @var LinterInterface[]
     */
    private array $linters = [];

    public function __construct(iterable $linters)
    {
        foreach ($linters as $linter) {
            $this->addLinter($linter);
        }
    }

    public function addLinter(LinterInterface $linter): void
    {
        $key = $linter->getKey();
        if (in_array($key, self::RESERVED_KEYS, true)) {
            throw new \LogicException(\sprintf('Used reserved key "%s"', $key));
        }
        if (array_key_exists($key, $this->linters)) {
            throw new \LogicException(\sprintf('Linter "%s" registered', $key));
        }

        $this->linters[$key] = $linter;
    }

    public function getRegisteredLinters(): array
    {
        return array_keys($this->linters);
    }

    public function getLinter(string $key): LinterInterface
    {
        if (!array_key_exists($key, $this->linters)) {
            throw new \LogicException(\sprintf('Linter "%s" not registered', $key));
        }

        return $this->linters[$key];
    }
}
