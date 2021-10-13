<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class FilesTransformedLine implements ReportLineInterface
{
    private string $domain;
    private string $file;
    private string $locale;

    public static function getEmptyReportMessage(): string
    {
        return 'All files have normalised state (they are transformed)';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Files which have abnormal state (they are not transformed)';
    }

    public function __construct(string $domain, string $locale, string $file)
    {
        $this->domain = $domain;
        $this->file = $file;
        $this->locale = $locale;
    }

    /**
     * @return array<int,string>
     */
    public static function getHeaders(): array
    {
        return ['domain', 'locale', 'file'];
    }

    /**
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return ['domain' => $this->domain, 'locale' => $this->locale, 'file' => $this->file, ];
    }
}
