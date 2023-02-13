<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

trait EmptyPresetsTrait
{
    /**
     * @return string[]
     */
    public function getPresets(): array
    {
        return [];
    }
}
