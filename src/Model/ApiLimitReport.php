<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class ApiLimitReport implements \JsonSerializable
{
    public string $uniqueKey;
    public string $startDate;
    public int $offset = 0;
    public string $service;

    public function __construct(?array $row)
    {
        if ($row) {
            $this->uniqueKey = $row['unique_key'];
            $this->startDate = $row['start_date'];
            $this->offset = (int) $row['offset'];
            $this->service = $row['service'];
        }
    }

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
