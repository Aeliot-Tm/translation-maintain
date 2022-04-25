<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class FilesTransformedLine extends AbstractLine
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
     * @return array<string,string>
     */
    protected function getNamedValues(): array
    {
        return ['domain' => $this->domain, 'locale' => $this->locale, 'file' => $this->file];
    }
}
