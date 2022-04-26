<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

trait EmptyPresetsTrait
{
    public function getPresets(): array
    {
        return [];
    }
}
