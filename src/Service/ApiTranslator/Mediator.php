<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator;

use Aeliot\Bundle\TransMaintain\Service\ApiTranslator\Facades\TranslatorFacadeInterface;

final class Mediator
{
    private FacadesRegistry $facadesRegistry;

    public function __construct(FacadesRegistry $facadesRegistry)
    {
        $this->facadesRegistry = $facadesRegistry;
    }

    public function translate(string $values, string $targetLocale, ?string $sourceLocale = null): string
    {
        return $this->getTranslator()->translate($values, $targetLocale, $sourceLocale);
    }

    public function translateBatch(array $values, string $targetLocale, ?string $sourceLocale = null): array
    {
        foreach ($values as $key => $value) {
            $values[$key] = $this->translate($value, $targetLocale, $sourceLocale);
        }

        return $values;
    }

    private function getTranslator(): TranslatorFacadeInterface
    {
        //TODO implement logic of selection another API
        return $this->facadesRegistry->getFacade(FacadesRegistry::FACADE_GOOGLE);
    }
}
