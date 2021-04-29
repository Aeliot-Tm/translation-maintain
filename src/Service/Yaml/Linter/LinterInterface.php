<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Model\ReportBag;

interface LinterInterface
{
    public function getKey(): string;

    public function lint(): ReportBag;
}
