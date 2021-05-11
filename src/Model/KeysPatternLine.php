<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class KeysPatternLine implements ReportLineInterface
{
    private string $domain;
    private string $languageId;
    /**
     * @var array<string>
     */
    private array $locales;

    public function __construct(string $domain, string $languageId, array $locales)
    {
        $this->domain = $domain;
        $this->languageId = $languageId;
        $this->locales = $locales;
    }

    /**
     * @var array<string>
     */
    public static function getHeaders(): array
    {
        return ['domain', 'invalid_language_id', 'locales'];
    }

    /**
     * @var array<string>
     */
    public function jsonSerialize(): array
    {
        return [
            'domain' => $this->domain,
            'invalid_language_id' => $this->languageId,
            'locales' => implode(', ', $this->locales),
        ];
    }
}
