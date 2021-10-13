<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;


final class ReportBag
{
    /**
     * @var ReportLineInterface[]
     */
    private array $lines = [];

    /**
     * @var class-string
     */
    private string $reportLineClass;

    /**
     * @param class-string $reportLineClass
     */
    public function __construct(string $reportLineClass)
    {
        $this->reportLineClass = $reportLineClass;
    }

    public function addLine(ReportLineInterface $line): void
    {
        if (!is_a($line, $this->reportLineClass)) {
            throw new \InvalidArgumentException(
                \sprintf('Invalid report line passed "%s". Expected "%s"', get_class($line), $this->reportLineClass)
            );
        }

        $this->lines[] = $line;
    }

    public function getHeaders(): array
    {
        /** @var ReportLineInterface $reportLineClass */
        $reportLineClass = $this->reportLineClass;

        return $reportLineClass::getHeaders();
    }

    /**
     * @return ReportLineInterface[]
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    public function getPriorMessage(): string
    {
        $prefix = 'Translation files linter report: ';
        /** @var ReportLineInterface $reportLineClass */
        $reportLineClass = $this->reportLineClass;
        if ($this->isEmpty()) {
            return \sprintf('%s<info>%s</info>', $prefix, $reportLineClass::getEmptyReportMessage());
        }

        return \sprintf('%s<fg=black;bg=yellow>%s</>', $prefix, $reportLineClass::getReportWithErrorsMessage());
    }

    public function isEmpty(): bool
    {
        return !$this->lines;
    }
}
