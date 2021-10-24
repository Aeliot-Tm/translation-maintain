<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

final class FilesMissedLine extends AbstractLine
{
    private string $domain;
    /**
     * @var array<int,string>
     */
    private array $omittedLanguages;

    public static function getEmptyReportMessage(): string
    {
        return 'All domains have files for all used locales';
    }

    public static function getReportWithErrorsMessage(): string
    {
        return 'Missed locales files for domains';
    }

    public function __construct(string $domain, array $omittedLanguages)
    {
        $this->domain = $domain;
        $this->omittedLanguages = $omittedLanguages;
    }

    /**
     * @return array<string,string>
     */
    protected function getNamedValues(): array
    {
        return ['domain' => $this->domain, 'omitted_languages' => $this->omittedLanguages];
    }
}
