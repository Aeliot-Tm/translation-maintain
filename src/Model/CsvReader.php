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

namespace Aeliot\Bundle\TransMaintain\Model;

/**
 * @deprecated since version 2.7.0. Use {@see \Aeliot\Bundle\TransMaintain\Model\CSV }
 *
 * @internal
 *
 * @template TKey as int
 *
 * @template-covariant TValue as array<string, string|null>
 *
 * @extends CSV<TKey, TValue>
 */
final class CsvReader extends CSV
{
}
