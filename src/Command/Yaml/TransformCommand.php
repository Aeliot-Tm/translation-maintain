<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command\Yaml;

use Aeliot\Bundle\TransMaintain\Exception\InvalidCommandParametersException;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileUpdater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class TransformCommand extends Command
{
    private FilesFinder $filesFinder;
    private FileUpdater $fileManager;

    public function __construct(FilesFinder $filesFinder, FileUpdater $fileManipulator)
    {
        parent::__construct('aeliot_trans_maintain:yaml:transform');

        $this->filesFinder = $filesFinder;
        $this->fileManager = $fileManipulator;
    }

    protected function configure(): void
    {
        $this->setDescription('Command for the transformation of YAML files with dot-chained keys.');
        $this->addArgument('file', InputArgument::OPTIONAL, 'Input file path to be updated.');
        $this->addArgument('output', InputArgument::OPTIONAL, 'Output file path. Optional. Incoming file will be updated if the argument omitted.');
        $this->addOption('all', 'a', InputOption::VALUE_NONE, 'Process all.');
        $this->addOption('domain', 'd', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Filter domains');
        $this->addOption('locale', 'l', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Filter locales');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            foreach ($this->getFiles($input) as [$pathIn, $pathOut]) {
                $message = ($pathIn === $pathOut) ? "Update file '$pathIn'." : "Transform file '$pathIn' to the file '$pathOut'.";
                $output->writeln($message, OutputInterface::VERBOSITY_VERBOSE);
                $this->fileManager->update($pathIn, $pathOut);
            }
        } catch (\Exception $exception) {
            $output->writeln(sprintf('<fg=white;bg=red>[ERROR] %s</>', $exception->getMessage()));

            return $exception->getCode();
        }

        return 0;
    }

    /**
     * @return iterable<array<string>>
     */
    private function getFiles(InputInterface $input): iterable
    {
        $isProcessAll = (bool) $input->getOption('all');
        $domains = $input->getOption('domain') ?: [];
        $locales = $input->getOption('locale') ?: [];
        if ($pathIn = (string) $input->getArgument('file')) {
            if ($isProcessAll || $domains || $locales) {
                throw new InvalidCommandParametersException('Do not pass any option when pass arguments', 1);
            }

            yield [$pathIn, $input->getArgument('output') ?: $pathIn];

            return;
        }

        $this->checkOptions($isProcessAll, $domains, $locales);

        foreach ($this->filterByDomains($this->filesFinder->getFilesMap(), $domains) as $localesFiles) {
            foreach ($this->filterByLocales($localesFiles, $locales) as $files) {
                foreach ($files as $file) {
                    yield [$file, $file];
                }
            }
        }
    }

    /**
     * @param string[] $domains
     * @param string[] $locales
     */
    private function checkOptions(bool $isProcessAll, array $domains, array $locales): void
    {
        if ($isProcessAll && ($domains || $locales)) {
            throw new InvalidCommandParametersException('Cannot process all files when filtering configured', 2);
        }
        if (!$isProcessAll && !$domains && !$locales) {
            throw new InvalidCommandParametersException('There is no file selected for the transformation', 3);
        }
    }

    /**
     * @param array<string,array<string,array<int,string>>> $files
     * @param string[] $domains
     *
     * @return array<string,array<string,array<int,string>>>
     */
    private function filterByDomains(array $files, array $domains): array
    {
        return $domains ? array_intersect_key($files, array_flip($domains)) : $files;
    }

    /**
     * @param array<string,array<int,string>> $localesFiles
     * @param string[] $locales
     *
     * @return array<string,array<int,string>>
     */
    private function filterByLocales(array $localesFiles, array $locales): array
    {
        return $locales ? array_intersect_key($localesFiles, array_flip($locales)) : $localesFiles;
    }
}
