<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Service\Yaml\Transformer;

use Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer\KeysCleaner;
use PHPUnit\Framework\TestCase;

final class KeysCleanerTest extends TestCase
{
    /**
     * @dataProvider getDataForTestTransform
     */
    public function testTransform(array $expected, array $income): void
    {
        self::assertEquals($expected, (new KeysCleaner())->transform($income));
    }

    public function getDataForTestTransform(): \Generator
    {
        yield [['a' => '*', 'b' => '*'], ['"a"' => '*', "'b'" => '*']];
        yield [['a' => ['b' => '*', 'c' => '*']], ['"a"' => ['"b"' => '*', "'c'" => '*']]];
        yield [['a"b"c' => '*'], ['a"b"c' => '*']];
    }
}
