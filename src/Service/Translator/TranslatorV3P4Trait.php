<?php

declare(strict_types=1);

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
        return $this->transInner(...func_get_args());
    }
}
