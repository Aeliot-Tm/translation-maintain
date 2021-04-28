<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class FilesMissedLine implements ReportLineInterface
{
    private string $domain;
    /**
     * @var array<string>
     */
    private array $omittedLanguages;

    public function __construct(string $domain, array $omittedLanguages)
    {
        $this->domain = $domain;
        $this->omittedLanguages = $omittedLanguages;
    }

    /**
     * @var array<string>
     */
    public static function getHeaders(): array
    {
        return ['domain', 'omitted_languages'];
    }

    /**
     * @var array<string>
     */
    public function jsonSerialize(): array
    {
        return ['domain' => $this->domain, 'omitted_languages' => implode(', ', $this->omittedLanguages)];
    }
}
