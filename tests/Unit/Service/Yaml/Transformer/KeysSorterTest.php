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

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Transformer;

use Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer\KeysSorter;
use PHPUnit\Framework\TestCase;

final class KeysSorterTest extends TestCase
{
    /**
     * @dataProvider getDataForTestTransform
     *
     * @param array<string,mixed> $expected
     * @param array<string,mixed> $income
     */
    public function testTransform(array $expected, array $income): void
    {
        self::assertSame($expected, (new KeysSorter())->transform($income));
    }

    public function getDataForTestTransform(): \Generator
    {
        yield [['a' => '*', 'b' => '*'], ['a' => '*', 'b' => '*']];
        yield [['a' => '*', 'b' => '*'], ['b' => '*', 'a' => '*']];
        yield [
            ['a' => '*', 'b' => ['c' => '*', 'd' => '*']],
            ['b' => ['d' => '*', 'c' => '*'], 'a' => '*'],
        ];
        yield [
            ['a' => ['b' => ['c' => ['d' => '*', 'e' => '*', 'f' => '*']]]],
            ['a' => ['b' => ['c' => ['d' => '*', 'e' => '*', 'f' => '*']]]],
        ];
        yield [
            ['a' => ['b' => ['c' => ['d' => '*', 'e' => '*', 'f' => '*']]]],
            ['a' => ['b' => ['c' => ['e' => '*', 'd' => '*', 'f' => '*']]]],
        ];
    }
}
