<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Translator;

use Symfony\Contracts\Translation\LocaleAwareInterface;

final class Translator extends AbstractTranslator implements LocaleAwareInterface
{
    use TranslatorV3P4Trait;
}
