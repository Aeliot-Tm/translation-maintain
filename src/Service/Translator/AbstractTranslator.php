<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Translator;

use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyRegister;
use Symfony\Bundle\FrameworkBundle\Translation\Translator as FrameworkTranslator;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\Reader\TranslationReaderInterface;
use Symfony\Component\Translation\Translator as ComponentTranslator;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractTranslator implements TranslatorInterface, TranslatorBagInterface
{
    /**
     * @var TranslatorInterface|TranslatorBagInterface|ComponentTranslator|FrameworkTranslator
     */
    protected $translator;

    private KeyRegister $keyRegister;

    /**
     * @var string[]
     */
    private array $loadedLocales = [];
    private string $position;
    private ?string $separateDirectory;
    private TranslationReaderInterface $translationReader;

    /**
     * @param TranslatorInterface|TranslatorBagInterface|ComponentTranslator|FrameworkTranslator $decoratedTranslator
     */
    public function __construct($decoratedTranslator, string $position, ?string $separateDirectory, KeyRegister $keyRegister, TranslationReaderInterface $translationReader)
    {
        $this->keyRegister = $keyRegister;
        $this->position = $position;
        $this->translator = $decoratedTranslator;
        $this->separateDirectory = $separateDirectory;
        $this->translationReader = $translationReader;
    }

    /**
     * @param string $name
     * @param array<mixed> $arguments
     */
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

    /**
     * @param string $locale
     *
     * @return void
     */
    public function setLocale($locale)
    {
        $this->translator->setLocale($locale);
    }

    /**
     * @param string $id
     * @param array<string|int,string|int> $parameters
     * @param string|null $domain
     * @param string|null $locale
     *
     * @return string
     */
    protected function transInner($id, array $parameters = [], $domain = null, $locale = null)
    {
        $translation = $this->translator->trans(...\func_get_args());
        if (('' !== ($id = (string) $id)) && $translation === $id) {
            $this->register($id, $domain, $locale);
        }

        return $translation;
    }

    /**
     * @param string|null $locale
     *
     * @return MessageCatalogueInterface
     */
    protected function getCatalogueInner($locale = null)
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
