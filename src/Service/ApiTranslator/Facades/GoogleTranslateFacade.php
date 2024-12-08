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

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator\Facades;

use Aeliot\Bundle\TransMaintain\Service\ApiTranslator\FacadesRegistry;
use Google\Cloud\Translate\V2\TranslateClient;

final class GoogleTranslateFacade implements TranslatorFacadeInterface
{
    private ?string $model;
    private TranslateClient $translateClient;

    public function __construct(?string $model, TranslateClient $translateClient)
    {
        $this->model = $model;
        $this->translateClient = $translateClient;
    }

    public function getKey(): string
    {
        return FacadesRegistry::FACADE_GOOGLE;
    }

    public function translate(string $value, string $targetLocale, ?string $sourceLocale = null): string
    {
        $options = ['target' => $targetLocale];
        if ($sourceLocale) {
            $options['source'] = $sourceLocale;
        }
        if ($this->model) {
            $options['model'] = $this->model;
        }

        $result = $this->translateClient->translate($value, $options);

        return $result['text'];
    }
}
