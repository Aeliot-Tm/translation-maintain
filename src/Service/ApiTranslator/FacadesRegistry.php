<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator;

use Aeliot\Bundle\TransMaintain\Service\ApiTranslator\Facades\TranslatorFacadeInterface;

final class FacadesRegistry
{
    public const FACADE_GOOGLE = 'google';

    /**
     * @var TranslatorFacadeInterface[]
     */
    private array $facades = [];

    public function __construct(iterable $facades)
    {
        foreach ($facades as $facade) {
            $this->addFacade($facade);
        }
    }

    private function addFacade(TranslatorFacadeInterface $facade): void
    {
        if (\array_key_exists($key = $facade->getKey(), $this->facades)) {
            throw new \LogicException(sprintf('Facade "%s" is registered', $key));
        }

        $this->facades[$key] = $facade;
    }

    public function getFacade(string $key): TranslatorFacadeInterface
    {
        if (!\array_key_exists($key, $this->facades)) {
            throw new \InvalidArgumentException(sprintf('Requested not registered facade "%s"', $key));
        }

        return $this->facades[$key];
    }
}
