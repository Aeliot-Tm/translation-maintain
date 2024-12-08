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

use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\LinterInterface;

final class LinterRegistry
{
    private const PRESETS = [LinterInterface::PRESET_ALL, LinterInterface::PRESET_BASE];

    /**
     * @var array<string,array<LinterInterface>>
     */
    private array $linters;

    /**
     * @param iterable<LinterInterface> $linters
     */
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
            throw new \LogicException(sprintf('Used reserved key "%s"', $key));
        }

        $presets = $linter->getPresets();
        $presets[] = LinterInterface::PRESET_ALL;

        if ($diff = array_diff($presets, self::PRESETS)) {
            throw new \LogicException(sprintf('Used invalid preset name(s): %s', implode(', ', $diff)));
        }

        foreach ($presets as $preset) {
            if (\array_key_exists($key, $this->linters[$preset])) {
                throw new \LogicException(sprintf('Linter "%s" registered', $key));
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
        if (!\array_key_exists($key, $this->linters[LinterInterface::PRESET_ALL])) {
            throw new \LogicException(sprintf('Linter "%s" not registered', $key));
        }

        return $this->linters[LinterInterface::PRESET_ALL][$key];
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
        return array_keys($this->linters[LinterInterface::PRESET_ALL]);
    }
}
