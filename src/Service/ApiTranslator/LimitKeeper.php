<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator;

use Aeliot\Bundle\TransMaintain\Exception\ApiLimitOutOfBoundsException;
use Aeliot\Bundle\TransMaintain\Model\ApiLimitReport;

final class LimitKeeper
{
    private LimitRepository $limitRepository;
    private array $limits;
    private ServiceUniqueIdProvider $uniqueIdProvider;

    public function __construct(
        LimitRepository $limitRepository,
        array $limits,
        ServiceUniqueIdProvider $uniqueIdProvider
    ) {
        $this->limitRepository = $limitRepository;
        $this->limits = $limits;
        $this->uniqueIdProvider = $uniqueIdProvider;
    }

    public function incrementLimit(string $value, string $serviceId): void
    {
        if (!$limit = $this->getLimit($serviceId)) {
            return;
        }

        $uniqueId = $this->uniqueIdProvider->getId($serviceId);
        $report = $this->getReport($uniqueId, $serviceId);
        $report->offset += mb_strlen($value);
        if ($report->offset > $limit) {
            throw new ApiLimitOutOfBoundsException('Limit of API has reached.');
        }

        $this->limitRepository->save($report);
    }

    private function getLimit(string $serviceId): ?int
    {
        if (!\array_key_exists($serviceId, $this->limits)) {
            throw new \DomainException(sprintf('Requested not registered service "%s"', $serviceId));
        }

        return $this->limits[$serviceId];
    }

    private function getReport(string $uniqueId, string $serviceId): ApiLimitReport
    {
        $startDate = (new \DateTime('first day of this month'))->format('Y-m-d');
        if (!$report = $this->limitRepository->findReport($uniqueId, $startDate)) {
            $report = new ApiLimitReport(null);
            $report->startDate = $startDate;
            $report->service = $serviceId;
            $report->uniqueKey = $uniqueId;
        }

        return $report;
    }
}
