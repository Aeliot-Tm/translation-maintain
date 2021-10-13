<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

interface ReportLineInterface extends \JsonSerializable
{
    public static function getEmptyReportMessage(): string;

    public static function getHeaders(): array;

    public static function getReportWithErrorsMessage(): string;
}
