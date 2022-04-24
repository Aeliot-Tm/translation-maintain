<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class SameValueLine extends AbstractLine
{
    private string $domain;
    private string $locale;
    private string $translation;
    private array $translationIds;

    public static function getEmptyReportMessage(): string
    {
        return 'There is no keys with same value';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Translation keys with same values';
    }

    public function __construct(string $domain, string $locale, string $translation, array $translationIds)
    {
        $this->domain = $domain;
        $this->locale = $locale;
        $this->translation = $translation;
        $this->translationIds = $translationIds;
    }

    protected function getNamedValues(): array
    {
        return [
            'domain' => $this->domain,
            'locale' => $this->locale,
            'translation' => $this->translation,
            'translation_ids' => $this->translationIds,
        ];
    }
}
