<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\LinterInterface;

final class LinterRegistry
{
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
        if (array_key_exists($key = $linter->getKey(), $this->linters)) {
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
