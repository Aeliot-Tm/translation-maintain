<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class KeysPatternLine extends AbstractLine
{
    private string $domain;
    /**
     * @var array<int,string>
     */
    private array $locales;
    private string $translationId;

    public static function getEmptyReportMessage(): string
    {
        return 'All translation keys match configured pattern';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Translation keys that are not match configured pattern';
    }

    public function __construct(string $domain, string $translationId, array $locales)
    {
        $this->domain = $domain;
        $this->translationId = $translationId;
        $this->locales = $locales;
    }

    /**
     * @return array<string,string>
     */
    protected function getNamedValues(): array
    {
        return [
            'domain' => $this->domain,
            'invalid_translation_id' => $this->translationId,
            'locales' => $this->locales,
        ];
    }
}
