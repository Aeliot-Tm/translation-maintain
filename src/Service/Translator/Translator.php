<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Translator;

use Symfony\Contracts\Translation\LocaleAwareInterface;

/**
 * @deprecated since v2.8.0. Use {@see TranslatorV5P0 }
 */
class Translator extends AbstractTranslator implements LocaleAwareInterface
{
    use TranslatorV3P4Trait;
}
