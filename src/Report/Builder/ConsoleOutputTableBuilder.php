<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Report\Builder;

use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleOutputTableBuilder
{
    private OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function render(ReportBag $reportBag): void
    {
        $table = new Table($this->output);
        $table->setHeaders($reportBag->getHeaders());
        foreach ($reportBag->getLines() as $line) {
            $table->addRow($line->jsonSerialize());
        }
        $table->render();
    }
}
