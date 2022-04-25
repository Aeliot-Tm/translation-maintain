<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Transformer;

use Aeliot\Bundle\TransMaintain\Service\Yaml\BranchInjector;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer\KeysTransformer;
use Generator;
use PHPUnit\Framework\TestCase;

final class KeysTransformerTest extends TestCase
{
    /**
     * @dataProvider getDataForTestTransform
     */
    public function testTransform(array $expected, array $income): void
    {
        self::assertEquals($expected, (new KeysTransformer(new BranchInjector()))->transform($income));
    }

    public function getDataForTestTransform(): Generator
    {
        // explode keys
        yield [['a' => ['b' => '*']], ['a.b' => '*']];
        yield [['a' => ['b' => ['c' => '*']]], ['a' => ['b.c' => '*']]];
        yield [['a' => ['b' => '*', 'c' => '*']], ['a.c' => '*', 'a.b' => '*']];
        yield [['a' => '*', 'a.b' => '*'], ['a' => '*', 'a.b' => '*']];
        yield [['a' => '*', 'a.b' => '*', 'a.c' => '*'], ['a.c' => '*', 'a.b' => '*', 'a' => '*']];
        yield [['a' => ['b' => ['c' => '*', 'd' => '*']]], ['a.b.c' => '*', 'a.b.d' => '*']];
        yield [['a' => ['b' => '*', 'b.c' => '*', 'b.d' => '*']], ['a.b' => '*', 'a.b.c' => '*', 'a.b.d' => '*']];
        yield [['a' => ['b' => '*', 'b.c' => '*', 'b.d' => '*']], ['a.b.c' => '*', 'a.b.d' => '*', 'a.b' => '*']];
        yield [
            ['a' => ['b' => '*', 'b.c' => '*', 'b.d' => '*', 'b.e' => '*']],
            ['a.b' => '*', 'a.b.c' => '*', 'a.b.d' => '*', 'a.b.e' => '*'],
        ];
        yield [
            ['a' => ['b' => ['c' => ['d' => ['e' => ['f' => '*', 'g' => '*', 'h' => '*']]]]]],
            ['a.b.c.d.e.f' => '*', 'a.b.c.d.e.g' => '*', 'a.b.c.d.e.h' => '*'],
        ];

        // explode child keys
        yield [
            ['a' => ['b' => ['c' => ['d' => '*', 'e' => '*']]]],
            ['a' => ['b' => ['c.d' => '*', 'c.e' => '*']]],
        ];

        // compress child
        yield [
            ['a' => ['b' => ['c' => '*', 'c.d' => '*', 'c.e' => '*']]],
            ['a.b.c' => '*', 'a.b.c.d' => '*', 'a.b.c.e' => '*'],
        ];
        yield [
            ['a' => ['b' => ['c' => '*', 'c.d' => ['e' => '*', 'f' => '*']]]],
            ['a.b.c' => '*', 'a.b.c.d.e' => '*', 'a.b.c.d.f' => '*'],
        ];
        yield [
            ['a' => ['b' => '*', 'b.c' => ['d' => ['e' => ['f' => '*', 'g' => '*']]]]],
            ['a.b' => '*', 'a.b.c.d.e.f' => '*', 'a.b.c.d.e.g' => '*'],
        ];

        // compress parent
        yield [
            ['a' => ['b' => ['c' => '*', 'c.d' => '*']]],
            ['a.b.c.d' => '*', 'a.b.c' => '*'],
        ];
        yield [
            ['a' => ['b' => '*', 'b.c' => '*']],
            ['a.b.c' => '*', 'a.b' => '*'],
        ];
        yield [
            ['a' => ['b' => ['c' => '*', 'c.d' => '*', 'c.e' => '*']]],
            ['a.b.c.e' => '*', 'a' => ['b' => ['c' => '*', 'c.d' => '*']]],
        ];

        // compress on root
        yield [
            ['a' => '*', 'a.b' => ['c' => ['d' => '*']]],
            ['a' => '*', 'a.b.c.d' => '*'],
        ];
        yield [
            ['a' => '*', 'a.b' => '*', 'a.b.c' => ['d' => '*']],
            ['a' => '*', 'a.b' => '*', 'a.b.c.d' => '*'],
        ];
        yield [
            ['a' => '*', 'a.b' => '*', 'a.b.c' => '*', 'a.b.c.d' => ['e' => '*']],
            ['a' => '*', 'a.b' => '*', 'a.b.c' => '*', 'a.b.c.d.e' => '*'],
        ];

        // don't compress children in case of parent keys collision
        yield [
            ['a' => '*', 'a.b' => ['c' => ['d' => '*']]],
            ['a' => '*', 'a.b' => ['c' => ['d' => '*']]],
        ];

        // not same values
        yield [['a' => ['b' => '1'], 'a.b' => '2'], ['a' => ['b' => '1'], 'a.b' => '2']];
        yield [['a' => ['b' => '1'], 'a.b' => '2'], ['a.b' => '2', 'a' => ['b' => '1']]];
        yield [
            ['a' => ['b' => ['c' => ['d' => '1'], 'c.d' => '2']]],
            ['a' => ['b' => ['c' => ['d' => '1']]], 'a.b' => ['c' => ['d' => '2']]],
        ];
        yield [
            ['a' => '*', 'a.b' => '*', 'a.b.c' => '*', 'a.b.c.d' => ['e' => '1'], 'a.b.c.d.e' => '2'],
            ['a' => '*', 'a.b' => '*', 'a.b.c' => '*', 'a.b.c.d' => ['e' => '1'], 'a.b.c.d.e' => '2'],
        ];
    }
}
