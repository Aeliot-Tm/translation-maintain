<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class FilesTransformedLine implements ReportLineInterface
{
    private string $domain;
    private string $file;
    private string $locale;

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
