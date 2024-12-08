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

namespace Aeliot\Bundle\TransMaintain\Service\ApiTranslator\Facades;

interface TranslatorFacadeInterface
{
    public function getKey(): string;

    public function translate(string $value, string $targetLocale, ?string $sourceLocale = null): string;
}
