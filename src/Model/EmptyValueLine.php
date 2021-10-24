<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class EmptyValueLine implements ReportLineInterface
{
    private string $domain;
    private string $languageId;
    private array $locales;

    public function __construct(string $domain, string $languageId, array $locales)
    {
        $this->domain = $domain;
        $this->locales = $locales;
        $this->languageId = $languageId;
    }

    public static function getEmptyReportMessage(): string
    {
        return 'There is no key with empty value';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Translation keys with empty values';
    }

    /**
     * @return array<int,string>
     */
    public static function getHeaders(): array
    {
        return ['domain', 'language_id', 'locales'];
    }

    /**
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return [
            'domain' => $this->domain,
            'language_id' => $this->languageId,
            'locales' => implode(', ', $this->locales),
        ];
    }
}
