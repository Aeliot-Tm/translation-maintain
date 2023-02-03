<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;

interface LinterInterface
{
    public const PRESET_ALL = 'all';
    public const PRESET_BASE = 'base';

    public function getKey(): string;

    /**
     * @return string[]
     */
    public function getPresets(): array;

    public function lint(LintYamlFilterDto $filterDto): ReportBag;
}
