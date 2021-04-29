<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class KeysDuplicatedLine implements ReportLineInterface
{
    private string $domain;
    private string $locale;
    private string $languageId;

    public function __construct(string $domain, string $locale, string $languageId)
    {
        $this->domain = $domain;
        $this->languageId = $languageId;
        $this->locale = $locale;
    }

    /**
     * @var array<string>
     */
    public static function getHeaders(): array
    {
        return ['domain', 'locale', 'language_id'];
    }

    /**
     * @var array<string>
     */
    public function jsonSerialize(): array
    {
        return [
            'domain' => $this->domain,
            'locale' => $this->locale,
            'language_id' => $this->languageId,
        ];
    }
}
