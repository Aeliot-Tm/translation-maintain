<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator;

use Aeliot\Bundle\TransMaintain\Model\ApiLimitReport;
use Aeliot\Bundle\TransMaintain\Model\CsvReader;
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

        $reader = new CsvReader($this->path);
        foreach ($reader as $row) {
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
            $reader = new CsvReader($this->path);
            foreach ($reader as $row) {
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

        $resource = fopen($this->path, 'wb');
        fputcsv($resource, array_keys(reset($table)));
        foreach ($table as $row) {
            fputcsv($resource, array_values($row));
        }
        fclose($resource);
    }

    private function isSeeked(array $row, string $uniqueId, string $startDate): bool
    {
        return $row['unique_key'] === $uniqueId && $row['start_date'] === $startDate;
    }
}
