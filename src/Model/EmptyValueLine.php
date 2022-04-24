<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class EmptyValueLine extends AbstractLine
{
    private string $domain;
    private array $locales;
    private string $translationId;

    public static function getEmptyReportMessage(): string
    {
        return 'There is no key with empty value';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Translation keys with empty values';
    }

    public function __construct(string $domain, string $translationId, array $locales)
    {
        $this->domain = $domain;
        $this->locales = $locales;
        $this->translationId = $translationId;
    }

    protected function getNamedValues(): array
    {
        return [
            'domain' => $this->domain,
            'translation_id' => $this->translationId,
            'locales' => $this->locales,
        ];
    }
}
