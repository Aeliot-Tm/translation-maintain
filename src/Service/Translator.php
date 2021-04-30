<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service;

use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyRegister;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class Translator implements LegacyTranslatorInterface, TranslatorInterface, TranslatorBagInterface
{
    private KeyRegister $keyRegister;
    /**
     * @var LegacyTranslatorInterface|TranslatorInterface|TranslatorBagInterface
     */
    private $translator;

    public function __construct($decoratedTranslator, KeyRegister $keyRegister)
    {
        $this->keyRegister = $keyRegister;
        $this->translator = $decoratedTranslator;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }

        if (method_exists($this->translator, $name)) {
            return $this->translator->{$name}(...$arguments);
        }

        throw new \BadMethodCallException(\sprintf('Called undefined method "%s"', $name));
    }

    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        $translation = $this->translator->trans(...func_get_args());
        if (('' !== ($id = (string) $id)) && $translation === $id) {
            $this->register($id, $domain, $locale);
        }

        return $translation;
    }

    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->translator->transChoice(...func_get_args());
    }

    public function setLocale($locale)
    {
        $this->translator->setLocale(...func_get_args());
    }

    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    public function getCatalogue($locale = null)
    {
        return $this->translator->getCatalogue(...func_get_args());
    }

    private function register(string $id, ?string $domain = null, ?string $locale = null): void
    {
        $domain ??= 'messages';

        /** @var MessageCatalogueInterface $catalogue */
        $catalogue = $this->translator->getCatalogue($locale);
        while (!$catalogue->defines($id, $domain)) {
            if (!$cat = $catalogue->getFallbackCatalogue()) {
                break;
            }
            $catalogue = $cat;
        }

        if (!$catalogue->has($id, $domain)) {
            $catalogue->has($id, $domain);
            $this->keyRegister->register($id, $domain, $catalogue->getLocale());
        }
    }
}