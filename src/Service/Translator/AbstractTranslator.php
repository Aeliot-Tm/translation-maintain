<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Translator;

use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyRegister;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\Reader\TranslationReaderInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractTranslator implements TranslatorInterface, TranslatorBagInterface
{
    private KeyRegister $keyRegister;

    /**
     * @var string[]
     */
    private array $loadedLocales = [];
    private string $position;
    private ?string $separateDirectory;

    /**
     * @var TranslatorInterface|TranslatorBagInterface|\Symfony\Component\Translation\Translator|\Symfony\Bundle\FrameworkBundle\Translation\Translator
     */
    private $translator;
    private TranslationReaderInterface $translationReader;

    public function __construct($decoratedTranslator, string $position, ?string $separateDirectory, KeyRegister $keyRegister, TranslationReaderInterface $translationReader)
    {
        $this->keyRegister = $keyRegister;
        $this->position = $position;
        $this->translator = $decoratedTranslator;
        $this->separateDirectory = $separateDirectory;
        $this->translationReader = $translationReader;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }

        if (method_exists($this->translator, $name)) {
            return $this->translator->{$name}(...$arguments);
        }

        throw new \BadMethodCallException(sprintf('Called undefined method "%s"', $name));
    }

    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        $translation = $this->translator->trans(...\func_get_args());
        if (('' !== ($id = (string) $id)) && $translation === $id) {
            $this->register($id, $domain, $locale);
        }

        return $translation;
    }

    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->translator->transChoice(...\func_get_args());
    }

    public function setLocale($locale): void
    {
        $this->translator->setLocale(...\func_get_args());
    }

    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    public function getCatalogue($locale = null)
    {
        $catalogue = $this->translator->getCatalogue(...\func_get_args());
        $loadedLocale = $locale ?? $this->getLocale();
        if ($this->separateDirectory && !\in_array($loadedLocale, $this->loadedLocales, true)) {
            $this->translationReader->read($this->separateDirectory, $catalogue);
            $this->loadedLocales[] = $loadedLocale;
        }

        return $catalogue;
    }

    private function register(string $id, ?string $domain = null, ?string $locale = null): void
    {
        if (KeyRegister::NO === $this->position) {
            return;
        }

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
            $catalogue->set($id, $id, $domain);
            $this->keyRegister->register($id, $domain, $catalogue->getLocale());
        }
    }
}
