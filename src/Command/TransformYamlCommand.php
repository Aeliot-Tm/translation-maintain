<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command;

use Aeliot\Bundle\TransMaintain\Service\Yaml\FileUpdater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class TransformYamlCommand extends Command
{
    private FileUpdater $fileManager;

    public function __construct(FileUpdater $fileManipulator)
    {
        parent::__construct('aeliot_trans_maintain:yaml:transform');

        $this->fileManager = $fileManipulator;
    }

    protected function configure(): void
    {
        $this->setDescription('Command for the transformation of YAML files with dot-chained keys.');
        $this->addArgument('file', InputArgument::REQUIRED, 'Input file path to be updated.');
        $this->addArgument('output', InputArgument::OPTIONAL, 'Output file path. Optional. Incoming file will be updated if the argument omitted.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pathIn = (string) $input->getArgument('file');
        $pathOut = $input->getArgument('output') ?: $pathIn;

        $this->fileManager->update($pathIn, $pathOut);

        return 0;
    }
}
