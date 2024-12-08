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

trait TranslatorV3P4Trait
{
    /**
     * @param string|null $locale
     */
    public function getCatalogue($locale = null)
    {
        return $this->getCatalogueInner($locale);
    }

    /**
     * @return string|null
     */
    public function getLocale()
    {
        return $this->translator->getLocale();
    }

    /**
     * @param string $id
     * @param array<string|int,string|int> $parameters
     * @param string|null $domain
     * @param string|null $locale
     *
     * @return string
     */
    public function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->transInner(...\func_get_args());
    }
}
