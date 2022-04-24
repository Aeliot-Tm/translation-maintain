<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class SameValueLine extends AbstractLine
{
    private string $domain;
    private array $languageIds;
    private string $locale;
    private string $translation;

    public static function getEmptyReportMessage(): string
    {
        return 'There is no keys with same value';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Translation keys with same values';
    }

    public function __construct(string $domain, string $locale, string $translation, array $languageIds)
    {
        $this->domain = $domain;
        $this->locale = $locale;
        $this->translation = $translation;
        $this->languageIds = $languageIds;
    }

    protected function getNamedValues(): array
    {
        return [
            'domain' => $this->domain,
            'locale' => $this->locale,
            'translation' => $this->translation,
            'language_ids' => $this->languageIds,
        ];
    }
}
