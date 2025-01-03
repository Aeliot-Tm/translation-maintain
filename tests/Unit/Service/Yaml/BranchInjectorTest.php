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

use Aeliot\Bundle\TransMaintain\Service\Yaml\BranchInjector;
use PHPUnit\Framework\TestCase;

final class BranchInjectorTest extends TestCase
{
    /**
     * @dataProvider getDataForSuccessfulInjectionTest
     *
     * @param array<string,mixed> $expected
     * @param array<string,mixed> $yaml
     * @param string|array<string,mixed> $value
     */
    public function testSuccessfulInjection(array $expected, array $yaml, string $key, $value): void
    {
        self::assertTrue((new BranchInjector())->inject($yaml, $key, $value));
        self::assertEquals($expected, $yaml);
    }

    /**
     * @dataProvider getDataForInjectionReturnFalseTest
     *
     * @param array<string,mixed> $yaml
     * @param string|array<string,mixed> $value
     */
    public function testInjectionReturnFalse(array $yaml, string $key, $value): void
    {
        self::assertFalse((new BranchInjector())->inject($yaml, $key, $value));
    }

    public function getDataForSuccessfulInjectionTest(): \Generator
    {
        // simple insert
        yield [['a' => '*'], [], 'a', '*'];
        yield [['a' => '*'], ['a' => '*'], 'a', '*'];
        yield [['a' => ['b' => ['c' => '*']]], [], 'a', ['b' => ['c' => '*']]];
        yield [['a' => ['b' => '1'], 'a.b' => '2'], ['a' => ['b' => '1']], 'a.b', '2'];

        // explode keys
        yield [['a' => ['b' => '*']], [], 'a.b', '*'];
        yield [['a' => ['b' => '*', 'c' => '*']], ['a' => ['b' => '*']], 'a.c', '*'];
        yield [['a' => '*', 'a.b' => '*'], ['a' => '*'], 'a.b', '*'];
        yield [['a' => ['b' => ['c' => '*', 'd' => '*']]], ['a' => ['b' => ['c' => '*']]], 'a.b.d', '*'];
        yield [['a' => ['b' => '*', 'b.c' => '*']], ['a' => ['b' => '*']], 'a.b.c', '*'];
        yield [
            ['a' => ['b' => ['c' => ['d' => ['e' => ['f' => '*', 'g' => '*']]]]]],
            ['a' => ['b' => ['c' => ['d' => ['e' => ['f' => '*']]]]]],
            'a.b.c.d.e.g',
            '*',
        ];
    }

    public function getDataForInjectionReturnFalseTest(): \Generator
    {
        yield [['a' => '1'], 'a', '2'];
        yield [['a' => ['b' => '1']], 'a', ['b' => '2']];
    }
}
