<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class EmptyValueLine extends AbstractLine
{
    private string $domain;
    private string $languageId;
    private array $locales;

    public static function getEmptyReportMessage(): string
    {
        return 'There is no key with empty value';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Translation keys with empty values';
    }

    public function __construct(string $domain, string $languageId, array $locales)
    {
        $this->domain = $domain;
        $this->locales = $locales;
        $this->languageId = $languageId;
    }

    protected function getNamedValues(): array
    {
        return [
            'domain' => $this->domain,
            'language_id' => $this->languageId,
            'locales' => $this->locales,
        ];
    }
}
