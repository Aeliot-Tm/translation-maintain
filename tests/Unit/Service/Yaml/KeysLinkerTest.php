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

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysLinker;
use PHPUnit\Framework\TestCase;

final class KeysLinkerTest extends TestCase
{
    /**
     * @dataProvider getDataForTestGlueKeys
     *
     * @param array<string,string> $expected
     * @param array<string,mixed> $income
     */
    public function testGlueKeys(array $expected, array $income): void
    {
        self::assertSame($expected, iterator_to_array((new KeysLinker())->glueKeys($income)));
    }

    /**
     * @return \Generator<array<array<string,mixed>>>
     */
    public function getDataForTestGlueKeys(): \Generator
    {
        yield [
            ['a.b' => '*', 'a.c' => '*', 'a.d.e' => '*'],
            ['a' => ['b' => '*', 'c' => '*', 'd' => ['e' => '*']]],
        ];
    }
}
