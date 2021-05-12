<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Command;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Report\Builder\ConsoleOutputTableBuilder;
use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\LinterInterface;
use Aeliot\Bundle\TransMaintain\Service\Yaml\LinterRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class LintYamlCommand extends Command
{
    private LinterRegistry $linterRegistry;

    public function __construct(LinterRegistry $linterRegistry)
    {
        parent::__construct('aeliot_trans_maintain:lint:yaml');

        $this->linterRegistry = $linterRegistry;
    }

    protected function configure(): void
    {
        $this->setDescription('Command for the sorting of yaml files');
        $this->addArgument('linter', InputArgument::IS_ARRAY, 'List of linters', [LinterRegistry::PRESET_BASE]);
        $this->addOption('domain', 'd', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Filter domains');
        $this->addOption('locale', 'l', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Filter locales');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $hasProblems = false;
        $filterDto = $this->createFilterDto($input);
        $reportBuilder = new ConsoleOutputTableBuilder($output);
        foreach ($this->getLinters($input) as $linter) {
            /** @var LinterInterface $linter */
            $reportBag = $linter->lint($filterDto);
            $hasProblems = $hasProblems || !$reportBag->isEmpty();
            $reportBuilder->render($reportBag);
        }

        return (int) $hasProblems;
    }

    private function createFilterDto(InputInterface $input): LintYamlFilterDto
    {
        $filterDto = new LintYamlFilterDto();
        if ($input->hasOption('domain')) {
            $filterDto->domains = $input->getOption('domain');
        }
        if ($input->hasOption('locale')) {
            $filterDto->locales = $input->getOption('locale');
        }

        return $filterDto;
    }

    /**
     * @return \Generator<LinterInterface>
     */
    private function getLinters(InputInterface $input): \Generator
    {
        $linters = (array) $input->getArgument('linter');
        if (\in_array(LinterRegistry::PRESET_ALL, $linters, true)) {
            if (count($linters) !== 1) {
                throw new \InvalidArgumentException('Preset "all" must be a single arguments');
            }

            yield from $this->linterRegistry->getPresetLinters(LinterRegistry::PRESET_ALL);
        }

        $linters = $this->transformPresetsToLinterKeys($linters);

        if ($invalid = array_diff($linters, $this->linterRegistry->getRegisteredLintersKeys())) {
            throw new \InvalidArgumentException(
                \sprintf('Requested not available linters: %s', implode(', ', $invalid))
            );
        }

        foreach ($linters as $linterKey) {
            yield $this->linterRegistry->getLinter($linterKey);
        }
    }

    /**
     * @param string[] $linters
     *
     * @return string[]
     */
    private function transformPresetsToLinterKeys(array $linters): array
    {
        if ($requestedPresets = array_intersect($this->linterRegistry->getExistingPresets(), $linters)) {
            $linters = array_diff($linters, $requestedPresets);
            $presetsLinters = array_map(
                fn(string $preset): array => $this->linterRegistry->getPresetLintersKeys($preset),
                $requestedPresets
            );
            $linters = array_unique(array_merge($linters, ...array_values($presetsLinters)));
        }

        return $linters;
    }
}
