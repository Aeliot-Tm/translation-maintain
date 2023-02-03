<?php

declare(strict_types=1);

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
