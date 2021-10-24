<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class KeysDuplicatedLine extends AbstractLine
{
    private string $domain;
    private string $locale;
    private string $languageId;

    public static function getEmptyReportMessage(): string
    {
        return 'There are no duplicated keys';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Duplicated translation keys';
    }

    public function __construct(string $domain, string $locale, string $languageId)
    {
        $this->domain = $domain;
        $this->languageId = $languageId;
        $this->locale = $locale;
    }

    /**
     * @return array<string,string>
     */
    protected function getNamedValues(): array
    {
        return [
            'domain' => $this->domain,
            'locale' => $this->locale,
            'duplicated_language_id' => $this->languageId,
        ];
    }
}
