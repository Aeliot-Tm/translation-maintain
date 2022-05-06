<?php

declare(strict_types=1);

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
     * @return \Generator<array<array<string,mixed>>
     */
    public function getDataForTestGlueKeys(): \Generator
    {
        yield [
            ['a.b' => '*', 'a.c' => '*', 'a.d.e' => '*'],
            ['a' => ['b' => '*', 'c' => '*', 'd' => ['e' => '*']]],
        ];
    }
}
