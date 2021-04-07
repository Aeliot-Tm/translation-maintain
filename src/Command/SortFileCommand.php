<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class SortFileCommand extends Command
{
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct('aeliot_trans_maintain:yaml:sort');

        $this->filesystem = $filesystem;
    }

    protected function configure(): void
    {
        $this->setDescription('Command for the sorting of yaml files');
        $this->addArgument('file', InputArgument::REQUIRED, 'Path to file to be updated');
        $this->addArgument('output', InputArgument::OPTIONAL, 'Output file path');
        $this->addOption('indent', 'i', InputOption::VALUE_REQUIRED, 'The amount of spaces to use for indentation of nested nodes', 4);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $yaml = Yaml::parseFile($this->getIncomingFilePath($input), Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
        $yaml = $this->sort($yaml);

        $indent = (int)$input->getOption('indent');
        $dumpFlags = Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK;
        $content = Yaml::dump($yaml, 100, $indent, $dumpFlags);
        $this->filesystem->dumpFile($this->getOutgoingFilePath($input), $content);
    }

    private function getIncomingFilePath(InputInterface $input): string
    {
        $pathIn = $input->getArgument('file');
        if (!$this->filesystem->exists($pathIn)) {
            throw new \InvalidArgumentException(\sprintf('Invalid path passed: "%s"', $this->filesystem));
        }

        return $pathIn;
    }

    private function getOutgoingFilePath(InputInterface $input): string
    {
        $pathOut = $input->getArgument('output') ?: $input->getArgument('file');
        $this->filesystem->mkdir(basename($pathOut));

        return $pathOut;
    }

    private function sort(array $yaml): array
    {
        ksort($yaml);
        foreach ($yaml as $key => $value) {
            if (is_array($value)) {
                $yaml[$key] = $this->sort($value);
            }
        }

        return $yaml;
    }
}
