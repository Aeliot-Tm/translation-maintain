<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class KeysMissedLine extends AbstractLine
{
    private string $domain;
    private string $languageId;
    /**
     * @var array<int,string>
     */
    private array $omittedLanguages;

    public static function getEmptyReportMessage(): string
    {
        return 'All locales of all domains are in the sync state. There are no missed translation keys';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Missed translation keys';
    }

    public function __construct(string $domain, string $languageId, array $omittedLanguages)
    {
        $this->domain = $domain;
        $this->languageId = $languageId;
        $this->omittedLanguages = $omittedLanguages;
    }

    /**
     * @return array<string,string>
     */
    protected function getNamedValues(): array
    {
        return [
            'domain' => $this->domain,
            'language_id' => $this->languageId,
            'omitted_languages' => $this->omittedLanguages,
        ];
    }
}
