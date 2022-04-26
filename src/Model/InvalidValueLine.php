<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class InvalidValueLine extends AbstractLine
{
    private string $domain;
    private string $locale;
    private string $translationId;

    public static function getEmptyReportMessage(): string
    {
        return 'There is no value which is match forbidden pattern';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Translation values which is match forbidden pattern';
    }

    public function __construct(string $domain, string $locale, string $translationId)
    {
        $this->domain = $domain;
        $this->locale = $locale;
        $this->translationId = $translationId;
    }

    protected function getNamedValues(): array
    {
        return [
            'domain' => $this->domain,
            'locale' => $this->locale,
            'translation_id' => $this->translationId,
        ];
    }
}
