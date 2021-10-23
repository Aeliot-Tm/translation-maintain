<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer\KeysSorter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SortFileCommand extends Command
{
    private FileManipulator $fileManipulator;
    private KeysSorter $keySorter;

    public function __construct(FileManipulator $fileManipulator, KeysSorter $keySorter)
    {
        parent::__construct('aeliot_trans_maintain:yaml:sort');

        $this->fileManipulator = $fileManipulator;
        $this->keySorter = $keySorter;
    }

    protected function configure(): void
    {
        $this->setDescription('Command for the sorting of yaml files');
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to file to be updated');
        $this->addArgument('output', InputArgument::OPTIONAL, 'Output file path');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pathIn = (string) $input->getArgument('file');
        $yaml = $this->fileManipulator->parse($pathIn);
        $yaml = $this->keySorter->transform($yaml);
        $pathOut = $input->getArgument('output') ?: $pathIn;
        $this->fileManipulator->dump($pathOut, $yaml);

        return 0;
    }
}
