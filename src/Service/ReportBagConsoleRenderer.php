<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service;

use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

final class ReportBagConsoleRenderer
{
    public function render(ReportBag $reportBag, OutputInterface $output): void
    {
        $output->writeln($reportBag->getPriorMessage());

        if (!$reportBag->isEmpty()) {
            $table = new Table($output);
            $table->setHeaders($reportBag->getHeaders());
            foreach ($reportBag->getLines() as $line) {
                $table->addRow($line->jsonSerialize());
            }
            $table->render();
        }
    }
}
