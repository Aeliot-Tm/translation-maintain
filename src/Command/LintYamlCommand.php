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

    private LinterRegistry $linterRegistry;

    public function __construct(LinterRegistry $linterRegistry)
    {
        parent::__construct('aeliot_trans_maintain:lint:yaml');

        $this->linterRegistry = $linterRegistry;
    }

    protected function configure(): void
    {
        $this->setDescription('Command for the sorting of yaml files');
        $this->addArgument('linter', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'List of linters', [self::ALL_LINTERS]);
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $reportBuilder = new ConsoleOutputTableBuilder($output);
        foreach ($this->getLinters($input) as $linter) {
            $reportBuilder->render($linter->lint());
        }
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
