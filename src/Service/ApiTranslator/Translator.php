<?php

declare(strict_types=1);

/*
 * This file is part of the TransMaintain.
 *
 * (c) Anatoliy Melnikov <5785276@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator;

use Aeliot\Bundle\TransMaintain\Service\ApiTranslator\Facades\TranslatorFacadeInterface;

final class Translator
{
    private FacadesRegistry $facadesRegistry;
    private LimitKeeper $limitKeeper;

    public function __construct(FacadesRegistry $facadesRegistry, LimitKeeper $limitKeeper)
    {
        $this->facadesRegistry = $facadesRegistry;
        $this->limitKeeper = $limitKeeper;
    }

    public function translate(string $value, string $targetLocale, ?string $sourceLocale = null): string
    {
        $translator = $this->getTranslator();
        $this->limitKeeper->incrementLimit($value, $translator->getKey());

        return $translator->translate($value, $targetLocale, $sourceLocale);
    }

    private function getTranslator(): TranslatorFacadeInterface
    {
        // TODO implement logic of selection another API
        return $this->facadesRegistry->getFacade(FacadesRegistry::FACADE_GOOGLE);
    }
}
