<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Transformer;

use Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer\KeysCleaner;
use Generator;
use PHPUnit\Framework\TestCase;

final class KeysCleanerTest extends TestCase
{
    /**
     * @dataProvider getDataForTestTransform
     *
     * @param array $expected
     * @param array $income
     * @return void
     */
    public function testTransform(array $expected, array $income): void
    {
        self::assertEquals($expected, (new KeysCleaner())->transform($income));
    }

    public function getDataForTestTransform(): Generator
    {
        yield [['a' => '*', 'b' => '*'], ['"a"' => '*', "'b'" => '*']];
        yield [['a' => ['b' => '*', 'c' => '*']], ['"a"' => ['"b"' => '*', "'c'" => '*']]];
    }
}