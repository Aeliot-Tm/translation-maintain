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

final class ApiLimitReport implements \JsonSerializable
{
    public string $uniqueKey;
    public string $startDate;
    public int $offset = 0;
    public string $service;

    /**
     * @param array{ unique_key: string, start_date: string, offset: int, service: string }|null $row
     */
    public function __construct(?array $row)
    {
        if ($row) {
            $this->uniqueKey = $row['unique_key'];
            $this->startDate = $row['start_date'];
            $this->offset = (int) $row['offset'];
            $this->service = $row['service'];
        }
    }

    /**
     * @return array{ unique_key: string, start_date: string, offset: int, service: string }
     */
    public function jsonSerialize(): array
    {
        return [
            'unique_key' => $this->uniqueKey,
            'start_date' => $this->startDate,
            'offset' => $this->offset,
            'service' => $this->service,
        ];
    }
}
