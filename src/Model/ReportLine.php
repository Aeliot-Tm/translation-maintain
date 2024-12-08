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

namespace Aeliot\Bundle\TransMaintain\Model;

final class ReportLine implements ReportLineInterface
{
    /**
     * @var array<string,string|array<string>>
     */
    private array $columns;

    /**
     * @param array<string,string|array<string>> $columns
     */
    public function __construct(array $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        $values = [];
        foreach ($this->columns as $key => $value) {
            if (\is_array($value)) {
                sort($value);
                $value = implode(', ', $value);
            }

            $values[$key] = $value;
        }

        return $values;
    }
}
