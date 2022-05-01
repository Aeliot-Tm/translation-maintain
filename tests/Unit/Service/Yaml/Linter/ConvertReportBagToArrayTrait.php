<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Model\ReportLineInterface;

trait ConvertReportBagToArrayTrait
{
    /**
     * @return array<array<string,mixed>>
     */
    private function convertReportBagToArrayTrait(ReportBag $bag): array
    {
        return array_map(static fn (ReportLineInterface $x): array => $x->jsonSerialize(), $bag->getLines());
    }
}
