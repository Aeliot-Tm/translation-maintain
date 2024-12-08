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
