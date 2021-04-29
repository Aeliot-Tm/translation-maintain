<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command;

use Aeliot\Bundle\TransMaintain\Report\Builder\ConsoleOutputTableBuilder;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\LinterInterface;
use Aeliot\Bundle\TransMaintain\Service\Yaml\LinterRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class LintYamlCommand extends Command
{
    private const ALL_LINTERS = 'all';
    private const BASE_LINTERS = 'base';

    private LinterRegistry $linterRegistry;

    public function __construct(LinterRegistry $linterRegistry)
    {
        parent::__construct('aeliot_trans_maintain:lint:yaml');

        $this->linterRegistry = $linterRegistry;
    }

    protected function configure(): void
    {
        $this->setDescription('Command for the sorting of yaml files');
        $this->addArgument('linter', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'List of linters', [self::BASE_LINTERS]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hasProblems = false;
        $reportBuilder = new ConsoleOutputTableBuilder($output);
        foreach ($this->getLinters($input) as $linter) {
            $reportBag = $linter->lint();
            $hasProblems = $hasProblems || !$reportBag->isEmpty();
            $reportBuilder->render($reportBag);
        }

        return (int) $hasProblems;
    }

    /**
     * @return iterable<LinterInterface>
     */
    private function getLinters(InputInterface $input): iterable
    {
        $possibleLinters = $this->linterRegistry->getRegisteredLinters();
        $linters = (array) $input->getArgument('linter');
        if (count($linters) === 1 && reset($linters) === self::ALL_LINTERS) {
            $linters = $possibleLinters;
        } elseif (count($linters) === 1 && reset($linters) === self::BASE_LINTERS) {
            $linters = $possibleLinters;
        } elseif ($invalid = array_diff($linters, $possibleLinters)) {
            throw new \InvalidArgumentException(
                \sprintf('Requested not available linters: %s', implode(', ', $invalid))
            );
        }

        foreach ($linters as $linterKey) {
            yield $this->linterRegistry->getLinter($linterKey);
        }
    }
}
