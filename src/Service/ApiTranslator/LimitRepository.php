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

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator;

use Aeliot\Bundle\TransMaintain\Model\ApiLimitReport;
use Aeliot\Bundle\TransMaintain\Model\CSV;
use Symfony\Component\Filesystem\Filesystem;

final class LimitRepository
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function findReport(string $uniqueId, string $startDate): ?ApiLimitReport
    {
        if (!file_exists($this->path)) {
            return null;
        }

        $csv = new CSV($this->path);
        foreach ($csv as $row) {
            /** @var array{unique_key: string, start_date: string, offset: int, service: string} $row */
            if ($this->isSeeked($row, $uniqueId, $startDate)) {
                return new ApiLimitReport($row);
            }
        }

        return null;
    }

    public function save(ApiLimitReport $report): void
    {
        (new Filesystem())->mkdir(\dirname($this->path));

        $table = [];
        $isFound = false;
        if (file_exists($this->path)) {
            $csv = new CSV($this->path);
            foreach ($csv as $row) {
                if ($this->isSeeked($row, $report->uniqueKey, $report->startDate)) {
                    $table[] = $report->jsonSerialize();
                    $isFound = true;
                } else {
                    $table[] = $row;
                }
            }
        }

        if (!$isFound) {
            $table[] = $report->jsonSerialize();
        }

        if (!$resource = fopen($this->path, 'wb')) {
            throw new \RuntimeException(sprintf('Cannot open file %s', $this->path));
        }
        /** @var array<string,mixed> $firstRow */
        $firstRow = reset($table);
        fputcsv($resource, array_keys($firstRow));
        foreach ($table as $row) {
            fputcsv($resource, array_values($row));
        }
        fclose($resource);
    }

    /**
     * @param array<string,mixed> $row
     */
    private function isSeeked(array $row, string $uniqueId, string $startDate): bool
    {
        return $row['unique_key'] === $uniqueId && $row['start_date'] === $startDate;
    }
}
