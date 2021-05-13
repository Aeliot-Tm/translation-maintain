<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator\Facades;

use Aeliot\Bundle\TransMaintain\Service\ApiTranslator\FacadesRegistry;
use Google\Cloud\Translate\V2\TranslateClient;

final class GoogleTranslateFacade implements TranslatorFacadeInterface
{
    private TranslateClient $translateClient;

    public function __construct(TranslateClient $translateClient)
    {
        $this->translateClient = $translateClient;
    }

    public function getKey(): string
    {
        return FacadesRegistry::FACADE_GOOGLE;
    }

    public function translate(string $value, string $targetLocale, ?string $sourceLocale = null): string
    {
        $options = ['target' => $targetLocale, 'model' => 'base'];
        if ($sourceLocale) {
            $options['source'] = $sourceLocale;
        }

        $result = $this->translateClient->translate($value, $options);

        return $result['text'];
    }
}
