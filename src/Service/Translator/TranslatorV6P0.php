<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Translator;

use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;

final class TranslatorV6P0 extends AbstractTranslator implements LocaleAwareInterface
{
    public function getCatalogue(string $locale = null): MessageCatalogueInterface
    {
        return $this->getCatalogueInner($locale);
    }

    public function getCatalogues(): array
    {
        return $this->translator->getCatalogues();
    }

    public function getLocale(): string
    {
        return $this->translator->getLocale();
    }

    /**
     * @param array<string|int,string|int> $parameters
     */
    public function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
    {
        return $this->transInner($id, $parameters, $domain, $locale);
    }
}
