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

namespace Aeliot\Bundle\TransMaintain\Command;

use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\FileMapBuilder;
use Aeliot\Bundle\TransMaintain\Service\ReportBagConsoleRenderer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class MissedTranslationsLoggedCommand extends Command
{
    private FileMapBuilder $fileMapBuilder;
    private Filesystem $filesystem;
    private ReportBagConsoleRenderer $reportBagConsoleRenderer;
    private ?string $separateDirectory;

    public function __construct(
        FileMapBuilder $fileMapBuilder,
        Filesystem $filesystem,
        ReportBagConsoleRenderer $reportBagConsoleRenderer,
        ?string $separateDirectory
    ) {
        parent::__construct('aeliot_trans_maintain:missed:logged');

        $this->fileMapBuilder = $fileMapBuilder;
        $this->filesystem = $filesystem;
        $this->reportBagConsoleRenderer = $reportBagConsoleRenderer;
        $this->separateDirectory = $separateDirectory;
    }

    protected function configure(): void
    {
        $this->setDescription('Command checks if missed translations logged');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->separateDirectory) {
            throw new \LogicException('Separate directory for the logging of missed translations is not configured');
        }

        $hasProblems = false;
        $bag = $this->createReportBag();

        foreach ($this->fileMapBuilder->buildFilesMap($this->getFiles()) as $domain => $localesFiles) {
            $locales = array_keys($localesFiles);
            $bag->addLine($domain, $locales);
            $hasProblems = true;
        }

        $this->reportBagConsoleRenderer->render($bag, $output);

        return (int) $hasProblems;
    }

    private function createReportBag(): ReportBag
    {
        $bag = new ReportBag(
            [
                'domain' => ['string'],
                'locale' => ['array'],
            ],
            'There are no missed translations logged',
            'Domains and locales with missed translations'
        );

        $bag->setMessagePrefix('Checking if missed translations logged: ');

        return $bag;
    }

    /**
     * @return iterable<SplFileInfo>
     */
    private function getFiles(): iterable
    {
        if (!$this->filesystem->exists($this->separateDirectory)) {
            return [];
        }

        return (new Finder())
            ->files()
            ->in($this->separateDirectory)
            ->filter(static fn (SplFileInfo $x) => !preg_match('/\.gitkeep$/', $x->getFilename()));
    }
}
