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

namespace Aeliot\Bundle\TransMaintain\Service\Translator;

use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;

final class TranslatorV6P0 extends AbstractTranslator implements LocaleAwareInterface
{
    public function getCatalogue(string $locale = null): MessageCatalogueInterface
    {
        return $this->getCatalogueInner($locale);
    }

    /**
     * @return MessageCatalogueInterface[]
     */
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
