<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class FilesMissedLine implements ReportLineInterface
{
    private string $domain;
    /**
     * @var array<int,string>
     */
    private array $omittedLanguages;

    public function __construct(string $domain, array $omittedLanguages)
    {
        $this->domain = $domain;
        $this->omittedLanguages = $omittedLanguages;
    }

    /**
     * @return array<int,string>
     */
    public static function getHeaders(): array
    {
        return ['domain', 'omitted_languages'];
    }

    /**
     * @return array<string,string>
     */
    public function jsonSerialize(): array
    {
        return ['domain' => $this->domain, 'omitted_languages' => implode(', ', $this->omittedLanguages)];
    }
}
