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

namespace Aeliot\Bundle\TransMaintain\Dto;

final class LintYamlFilterDto
{
    /**
     * @var string[]|null
     */
    public ?array $domains = null;
    /**
     * @var string[]|null
     */
    public ?array $locales = null;
}
