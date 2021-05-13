<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator\Facades;

interface TranslatorFacadeInterface
{
    public function getKey(): string;

    public function translate(string $value, string $targetLocale, ?string $sourceLocale = null): string;
}
