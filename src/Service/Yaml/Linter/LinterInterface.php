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
