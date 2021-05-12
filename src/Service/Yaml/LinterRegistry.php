<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\LinterInterface;

final class LinterRegistry
{
    public const PRESET_ALL = 'all';
    public const PRESET_BASE = 'base';

    private const PRESETS = [self::PRESET_ALL, self::PRESET_BASE];

    /**
     * @var array<array<LinterInterface>>
     */
    private array $linters;

    public function __construct(iterable $linters)
    {
        $this->linters = array_fill_keys(self::PRESETS, []);

        foreach ($linters as $linter) {
            $this->addLinter($linter);
        }
    }

    public function addLinter(LinterInterface $linter): void
    {
        $key = $linter->getKey();
        if (\in_array($key, self::PRESETS, true)) {
            throw new \LogicException(\sprintf('Used reserved key "%s"', $key));
        }

        $presets = $linter->getPresets();
        $presets[] = self::PRESET_ALL;

        if ($diff = array_diff($presets, self::PRESETS)) {
            throw new \LogicException(\sprintf('Used invalid preset name(s): %s', implode(', ', $diff)));
        }

        foreach ($presets as $preset) {
            if (\array_key_exists($key, $this->linters[$preset])) {
                throw new \LogicException(\sprintf('Linter "%s" registered', $key));
            }
            $this->linters[$preset][$key] = $linter;
        }
    }

    /**
     * @return string[]
     */
    public function getExistingPresets(): array
    {
        return array_keys($this->linters);
    }

    public function getLinter(string $key): LinterInterface
    {
        if (!\array_key_exists($key, $this->linters[self::PRESET_ALL])) {
            throw new \LogicException(\sprintf('Linter "%s" not registered', $key));
        }

        return $this->linters[self::PRESET_ALL][$key];
    }

    /**
     * @return LinterInterface[]
     */
    public function getPresetLinters(string $preset): array
    {
        if (!\array_key_exists($preset, $this->linters)) {
            throw new \InvalidArgumentException(sprintf('Preset "%s" not registered', $preset));
        }

        return $this->linters[$preset];
    }

    /**
     * @return string[]
     */
    public function getPresetLintersKeys(string $preset): array
    {
        if (!\array_key_exists($preset, $this->linters)) {
            throw new \InvalidArgumentException(sprintf('Preset "%s" not registered', $preset));
        }

        return array_keys($this->linters[$preset]);
    }

    /**
     * @return string[]
     */
    public function getRegisteredLintersKeys(): array
    {
        return array_keys($this->linters[self::PRESET_ALL]);
    }
}
