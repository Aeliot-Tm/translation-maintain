<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Model;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class ReportBag
{
    /**
     * @var string[]
     */
    private array $columns;
    private int $columnsCount;

    /**
     * @var ReportLineInterface[]
     */
    private array $lines = [];
    private string $messageEmptyReport;
    private string $messagePrefix = 'Translation files linter report: ';
    private string $messageReportWithErrors;
    private OptionsResolver $resolver;

    /**
     * @param array<string,string|array<string>> $columnsConfig
     */
    public function __construct(array $columnsConfig, string $messageEmptyReport, string $messageReportWithErrors)
    {
        if (!$columnsConfig) {
            throw new \InvalidArgumentException('Columns are not configured');
        }
        $this->columns = array_keys($columnsConfig);
        $this->messageEmptyReport = $messageEmptyReport;
        $this->messageReportWithErrors = $messageReportWithErrors;

        $this->columnsCount = \count($this->columns);
        $this->resolver = new OptionsResolver();

        $this->resolver->setDefaults(array_fill_keys($this->columns, null));
        foreach ($columnsConfig as $column => $allowedTypes) {
            $this->resolver->setAllowedTypes($column, $allowedTypes);
        }
    }

    /**
     * @param string|string[]|mixed ...$values
     */
    public function addLine(...$values): void
    {
        if (\count($values) !== $this->columnsCount) {
            throw new \InvalidArgumentException('Invalid values count');
        }

        $this->lines[] = new ReportLine($this->resolver->resolve(array_combine($this->columns, $values)));
    }

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return $this->columns;
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
        if ($this->isEmpty()) {
            return sprintf('%s<info>%s</info>', $this->messagePrefix, $this->messageEmptyReport);
        }

        return sprintf('%s<fg=black;bg=yellow>%s</>', $this->messagePrefix, $this->messageReportWithErrors);
    }

    public function isEmpty(): bool
    {
        return !$this->lines;
    }

    public function setMessagePrefix(string $messagePrefix): void
    {
        $this->messagePrefix = $messagePrefix;
    }
}
