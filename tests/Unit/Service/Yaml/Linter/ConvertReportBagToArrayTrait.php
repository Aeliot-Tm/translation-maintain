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
