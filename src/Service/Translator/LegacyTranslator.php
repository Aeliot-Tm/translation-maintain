<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Translator;

use Symfony\Component\Translation\TranslatorInterface as LegacyTranslatorInterface;

final class LegacyTranslator extends AbstractTranslator implements LegacyTranslatorInterface
{
    use TranslatorV3P4Trait;

    /**
     * @param string $id
     * @param int $number
     * @param array<string|int,string|int> $parameters
     * @param string|null $domain
     * @param string|null $locale
     *
     * @return string
     */
    public function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
    {
        return $this->translator->transChoice(...\func_get_args());
    }
}
