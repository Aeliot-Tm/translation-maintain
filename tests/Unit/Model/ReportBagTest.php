<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Unit\Model;

use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Model\ReportLineInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class ReportBagTest extends TestCase
{
    public function testColumnsNotConfigured(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Columns are not configured');
        new ReportBag([], 'message A', 'message B');
    }

    /**
     * @dataProvider getDataForTestCountLines
     */
    public function testCountLines(int $expected, array $columnConfig, array $values): void
    {
        $bag = new ReportBag($columnConfig, 'message A', 'message B');
        array_walk($values, static fn (array $lineValues) => $bag->addLine(...$lineValues));
        self::assertCount($expected, $bag->getLines());
    }

    /**
     * @dataProvider getDataForTestGetHeaders
     */
    public function testGetHeaders(array $expected, array $columnConfig): void
    {
        self::assertSame($expected, (new ReportBag($columnConfig, 'message A', 'message B'))->getHeaders());
    }

    /**
     * @dataProvider getDataForTestInvalidLineValuesCount
     */
    public function testInvalidLineValuesCount(array $columnConfig, array $lineValues): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid values count');

        (new ReportBag($columnConfig, 'message A', 'message B'))->addLine(...$lineValues);
    }

    /**
     * @dataProvider getDataForTestInvalidValueType
     */
    public function testInvalidValueType(array $columnConfig, array $lineValues): void
    {
        $this->expectException(InvalidOptionsException::class);

        (new ReportBag($columnConfig, 'message A', 'message B'))->addLine(...$lineValues);
    }

    public function testLineIsReportLineInterface(): void
    {
        $bag = new ReportBag(['a' => 'string'], 'message A', 'message B');
        $bag->addLine('A');
        self::assertInstanceOf(ReportLineInterface::class, $bag->getLines()[0]);
    }

    public function testPriorMessageContainsConfiguredMessages(): void
    {
        $messageEmptyReport = 'Message for empty report';
        $messageReportWithErrors = 'Message for report with errors';
        $bag = new ReportBag(['a' => 'string'], $messageEmptyReport, $messageReportWithErrors);

        self::assertStringContainsString($messageEmptyReport, $bag->getPriorMessage());
        self::assertStringNotContainsString($messageReportWithErrors, $bag->getPriorMessage());

        $bag->addLine('A');

        self::assertStringContainsString($messageReportWithErrors, $bag->getPriorMessage());
        self::assertStringNotContainsString($messageEmptyReport, $bag->getPriorMessage());
    }

    public function getDataForTestCountLines(): \Generator
    {
        yield [1, ['a' => 'string'], [['A']]];
        yield [2, ['a' => 'string'], [['A'], ['B']]];
        yield [3, ['a' => 'string'], [['A'], ['B'], ['C']]];
    }

    public function getDataForTestGetHeaders(): \Generator
    {
        yield [['a'], ['a' => 'string']];
        yield [['a', 'b'], ['a' => 'string', 'b' => 'string']];
        yield [['a', 'z', 'b'], ['a' => 'string', 'z' => 'string', 'b' => 'string']];
    }

    public function getDataForTestInvalidLineValuesCount(): \Generator
    {
        yield [['a' => 'string', 'b' => 'string'], ['x']];
        yield [['a' => 'string', 'b' => 'string'], ['x', 'y', 'z']];
    }

    public function getDataForTestInvalidValueType(): \Generator
    {
        yield [['a' => 'string'], [['x']]];
        yield [['a' => 'array'], ['x']];
    }
}
