<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class KeysMissedLine extends AbstractLine
{
    private string $domain;
    /**
     * @var array<int,string>
     */
    private array $omittedLanguages;
    private string $translationId;

    public static function getEmptyReportMessage(): string
    {
        return 'All locales of all domains are in the sync state. There are no missed translation keys';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Missed translation keys';
    }

    public function __construct(string $domain, string $translationId, array $omittedLanguages)
    {
        $this->domain = $domain;
        $this->translationId = $translationId;
        $this->omittedLanguages = $omittedLanguages;
    }

    /**
     * @return array<string,string>
     */
    protected function getNamedValues(): array
    {
        return [
            'domain' => $this->domain,
            'translation_id' => $this->translationId,
            'omitted_languages' => $this->omittedLanguages,
        ];
    }
}
