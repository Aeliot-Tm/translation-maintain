<?php

declare(strict_types=1);

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
